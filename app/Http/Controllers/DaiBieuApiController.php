<?php

namespace App\Http\Controllers;

use App\Enums\LoaiTaiLieuEnum;
use App\Enums\TrangThaiKhoaHopEnum;
use App\Models\DaibieuKhoahop;
use App\Models\KhoaHop;
use http\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class DaiBieuApiController extends Controller
{
    public $successStatus = Response::HTTP_OK;

    public function GetDsDaiBieu(Request $request)
    {
        try {
            $khoahop = KhoaHop::where('trang_thai', TrangThaiKhoaHopEnum::HoatDong)
                                ->first();
            $term = $request->term;
            $daibieus = [];
            if ($term != null && $term != "") {
                $daibieus = $this->fulltextSearchDaiBieu($khoahop->id, $term);
            } else {
                $daibieus = $this->getDaiBieus($khoahop->id);
            }
            return response()->json([
                'error_code' => 0,
                'data' =>  $daibieus,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 1,
                'message' => json_encode($e),
            ], Response::HTTP_OK);
        }
    }

    public function SearchDaiBieu(Request $request)
    {
        try {
            $term = $request->term;
            $khoahop = KhoaHop::where('trang_thai', TrangThaiKhoaHopEnum::HoatDong)
                ->first();
            $daibieus = $this->fulltextSearchDaiBieu($khoahop->id, $term);
            return response()->json([
                'error_code' => 0,
                'data' =>  $daibieus,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 1,
                'message' => json_encode($e),
            ], Response::HTTP_OK);
        }
    }

    public function ChangePassword(Request $request) {
        $currentUser = Auth::user();
        if ($currentUser == null) {
            return response()->json([
                'error_code' => 1,
                'message' =>  'Người dùng không tồn tại',
            ], Response::HTTP_OK);
        }
        $oldpass = $request->oldpassword;
        $newpass = $request->newpassword;
        if (!Hash::check($oldpass, $currentUser->password)) {
            return response()->json([
                'error_code' => 1,
                'message' => "Mật khẩu cũ không chính xác"
            ], Response::HTTP_OK);
        }
        if ($oldpass == $newpass) {
            return response()->json([
                'error_code' => 1,
                'message' => "Mật khẩu không có sự thay đổi"
            ], Response::HTTP_OK);
        }
        if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z]{6,16}$/', $newpass)) {
            return response()->json([
                'error_code' => 1,
                'message' =>  'Mật khẩu phải bao gồm kí tự số, chữ cái và có độ dài từ 6 đến 16 kí tự',
            ], Response::HTTP_OK);
        }
        DB::beginTransaction();
        try {
            DB::table('users')
            ->where('id', $currentUser->id)
            ->update([
                'password' => Hash::make($newpass),
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUser->id
            ]);
            DB::commit();
            return response()->json([
                'error_code' => 0,
                'message' => 'Đổi mật khẩu thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => 1,
                'message' => 'Đổi mật khẩu không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }
    // Lấy danh sách đại biểu theo khóa họp
    private function getDaiBieus($khoahopid) {
        $configHost = env('APP_URL')."/storage/";
        $defaultAvatar = env('APP_URL')."/storage/users/default.png";
        $query =
            "SELECT
                khoahop.user_id,
                u.name,
                u.email,
                CASE
                    WHEN u.avatar IS NULL THEN ?
                    ELSE CONCAT(?, u.avatar)
                END AS avatar,
                u.username,
                u.ngay_sinh,
                u.gioi_tinh,
                u.tongiao,
                u.que_quan,
                u.trinhdo_hocvan,
                u.trinhdo_chinhtri,
                u.nghenghiep_chucvu,
                u.trinhdo_chuyenmon,
                dantoc.dan_toc
            FROM hdnd_app.daibieu_khoahop khoahop
            INNER JOIN hdnd_app.users u
                ON khoahop.user_id = u.id
            LEFT JOIn hdnd_app.dan_toc dantoc
                ON u.dantoc_id = dantoc.id
            WHERE
                khoahop.khoahop_id = ?
            ORDER BY khoahop.stt";
        $daibieus = DB::select($query, [$defaultAvatar, $configHost, $khoahopid]);
        return $daibieus;
    }

    private function fulltextSearchDaiBieu($khoahopid, $term) {
        $term = $term.'*';
        $configHost = env('APP_URL')."/storage/";
        $defaultAvatar = env('APP_URL')."/storage/users/default.png";
        DB::enableQueryLog();
        $query =
            "SELECT
                khoahop.user_id,
                u.name,
                u.email,
                CASE
                    WHEN u.avatar IS NULL THEN ?
                    ELSE CONCAT(?, u.avatar)
                END AS avatar,
                u.username,
                u.ngay_sinh,
                u.gioi_tinh,
                u.que_quan,
                u.tongiao,
                u.trinhdo_hocvan,
                u.trinhdo_chinhtri,
                u.nghenghiep_chucvu,
                u.trinhdo_chuyenmon,
                dantoc.dan_toc
            FROM hdnd_app.daibieu_khoahop khoahop
            INNER JOIN hdnd_app.users u
                ON khoahop.user_id = u.id
            LEFT JOIN hdnd_app.dan_toc dantoc
                ON u.dantoc_id = dantoc.id
            WHERE
                khoahop.khoahop_id = ?
                AND MATCH (u.name)
                AGAINST (? IN BOOLEAN MODE)
            ORDER BY khoahop.stt";
        $daibieus = DB::select($query, [$defaultAvatar, $configHost, $khoahopid, $term]);
        return $daibieus;
    }
}
