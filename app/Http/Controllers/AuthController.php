<?php

namespace App\Http\Controllers;

use App\Enums\TrangThaiDiemDanhEnum;
use App\Enums\TrangThaiKyHopEnum;
use App\Models\DanToc;
use http\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use App\Helpers\SmsUtils;
use App\Helpers\ReflectionUtils;
use App\Helpers\ValidateHelper;
use App\Jobs\PushNotificationJob;
use App\Models\DiemDanh;
use App\Models\KyHop;
use App\Models\LichHop;

class AuthController extends Controller
{

    public $successStatus = Response::HTTP_OK;

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $accept = $request->header('Accept');

        if ($accept == null || $accept != 'application/json'){
            return response()->json([
                'error_code' => 1,
                'message' => "HTTP_NOT_ACCEPTABLE"
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string'
            ]);

            if (!Auth::attempt(['username' => $request->username, 'password' => $request->password, 'deleted_at' => null])
                && !Auth::attempt(['phone' => $request->username, 'password' => $request->password, 'deleted_at' => null])
            )
                return response()->json([
                    'error_code' => 1,
                    'message' => "Thông tin đăng nhập không chính xác"
                ], Response::HTTP_OK);

            if (Auth::user()->is_active == 0) {
                return response()->json([
                    'error_code' => 1,
                    'message' => "Người dùng chưa được kích hoạt"
                ], Response::HTTP_OK);
            }

            $oClient = OClient::where('password_client', 1)->first();

            $token = $this->getTokenAndRefreshTokenByPassword($oClient, \auth()->user()->email, $request->password);
            return response()->json([
                'error_code' => 0,
                'message' => 'Thành công',
                'data' => [
                    'token' => $token
                ]
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            response()->json([
                'error_code' => 1,
                'message' => $e->getMessage(),
            ], Response::HTTP_OK);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $accessToken = Auth::user()->token();
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $accessToken->revoke();
        return response()->json([
            'error_code' => 0,
            'message' => "Thành công",
        ], Response::HTTP_OK);
    }

    public function getInfors(Request $request) {
        $currentUser = Auth::user();
        $userInfo = $this->getUserInfor();
        $kyHop = $this->getUserKyHop();

        $kyhop = KyHop::where('tenant_id', $currentUser->tenant_id)
                        ->where('trang_thai', TrangThaiKyHopEnum::DangDienRa)
                        ->first();
        $kyhopId = $kyhop != null ? $kyhop->id : 0;
        $lichHops = LichHop::where('kyhop_id', $kyhopId)
                            ->whereHas('thoi_gian', function($q){
                                $q->whereNull('deleted_at');
                            })
                            ->where('phonghop_id', '!=', 0)
                            ->get();
        $buoihopId = 0;
        foreach($lichHops as $buoihop) {
            $isValid = ValidateHelper::CheckThoiGianDiemDanh($buoihop->thoi_gian->ngay_dienra, $buoihop->buoi_hop);
            if ($isValid) {
                $buoihopId = $buoihop->id;
                break;
            }
        }
        // Lấy trạng thái điểm danh
        $diemDanh = DiemDanh::where('lichhop_id', $buoihopId)
                ->where(function($q) {
                    $q->where('trang_thai', TrangThaiDiemDanhEnum::DiemDanhBangApp)
                    ->orWhere('trang_thai', TrangThaiDiemDanhEnum::DiemDanhThuCong);
                })
                ->where('user_id', $currentUser->id)
                ->select('user_id','trang_thai')
                ->first();
        return response()->json([
            'error_code' => 0,
            'message' => 'Thành công',
            'data' => [
                'user_info' => $userInfo,
                'ky_hop' => $kyHop,
                'diem_danh' => $diemDanh != null ? $diemDanh->trang_thai : TrangThaiDiemDanhEnum::VangMat,
                'use_firebase' => intval(setting('site.usefirebase'))
            ]
        ], Response::HTTP_OK);
    }
    /* Lấy token mới từ refresh_token */
    public function getTokenAndRefreshTokenByRefreshToken(Request $request)
    {
        $oClient = OClient::where('password_client', 1)->first();

        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'scope' => ''
        ];
        $request = Request::create('/oauth/token', 'POST', $data);
        $result = json_decode(app()->handle($request)->getContent());

        return response()->json([
            'error_code' => 0,
            'message' => "Thành công",
            'data' => $result
        ], Response::HTTP_OK);

    }

    /* Lấy token mới từ password */
    public function getTokenAndRefreshTokenByPassword(OClient $oClient, $email, $password)
    {
        $oClient = OClient::where('password_client', 1)->first();
        $data = [
            'grant_type' => 'password',
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'username' => $email,
            'password' => $password,
            'scope' => ''
        ];
        $request = Request::create('/oauth/token', 'POST', $data);
        return json_decode(app()->handle($request)->getContent());
    }

    public function getUserInfor()
    {
        $configHost = env('APP_URL')."/storage/";
        $defaultAvatar = env('APP_URL')."/storage/users/default.png";
        $user_id = Auth::id();
        $query =
        "SELECT
            r.id role_id,
            r.NAME role_name,
            u.name ho_ten,
            u.username,
            u.email,
            u.phone,
            dv.ten_donvi don_vi_ct,
            cv.chuc_vu,
            DATE_FORMAT(u.ngay_sinh, '%d/%m/%Y') ngay_sinh,
            u.gioi_tinh,
            u.que_quan,
            CASE
                WHEN u.avatar IS NULL THEN ?
                ELSE CONCAT(?, u.avatar)
            END AS avatar,
            u.tenant_id
        FROM
            users u
        LEFT JOIN roles r ON u.role_id = r.id
        LEFT JOIN don_vi dv ON dv.id = u.donvi_id
        LEFT JOIN chuc_vu cv ON cv.id = u.chucvu_id
        WHERE u.deleted_at IS NULL
        AND u.id = ?";
        return DB::select($query, [$defaultAvatar, $configHost, $user_id])[0];
    }

    public function getUserKyHop()
    {
        $user_id = Auth::id();
        $query =
        "SELECT
            kh.id,
            kh.ten_ky_hop,
            kh.dia_diem,
            dbkh.vai_tro,
            dbkh.quyen_bieuquyet,
            dbkh.stt,
            tgkp.thoi_gian,
            kh.tenant_id,
            dv.name AS tenant_name
        FROM ky_hop kh
        INNER JOIN daibieu_kyhop dbkh
            ON dbkh.kyhop_id = kh.id
        INNER JOIN tenants dv
            ON kh.tenant_id = dv.id
        LEFT JOIN (SELECT
                    CONCAT('Từ ngày ', DATE_FORMAT(MIN(tgkp.ngay_dienra),'%d/%m/%Y') ,' đến ngày ', DATE_FORMAT(MAX(tgkp.ngay_dienra),'%d/%m/%Y')) thoi_gian,
                    tgkp.kyhop_id
                    FROM thoigian_kyhop tgkp
                    GROUP BY tgkp.kyhop_id) tgkp
            ON tgkp.kyhop_id = kh.id
        WHERE
            kh.trang_thai = 1 AND
            kh.deleted_at IS NULL AND
            dbkh.user_id = ?";
        return DB::select($query, [$user_id]);
    }
}
