<?php

namespace App\Http\Controllers;

use App\Enums\BuoiHopEnum;
use App\Enums\GiaTriBieuQuyetEnum;
use App\Enums\TrangThaiBieuQuyetEnum;
use App\Enums\TrangThaiDiemDanhEnum;
use App\Enums\TrangThaiKyHopEnum;
use App\Enums\VaiTroEnum;
use App\Helpers\FireBaseHelper;
use App\Models\DaiBieuKyHop;
use App\Models\LichHop;
use App\Helpers\LocationHelper;
use App\Models\BieuQuyet;
use App\Models\DiemDanh;
use App\Models\KetquaBieuquyetChitiet;
use http\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;


class BieuQuyetApiController extends Controller
{

    public $successStatus = Response::HTTP_OK;

    public function getBieuQuyet(Request $request)
    {
        $kyhopid = $request->kyhopid;
        $bieuQuyet = BieuQuyet::where('kyhop_id', $kyhopid)
                            ->where('trang_thai', TrangThaiBieuQuyetEnum::DangBieuQuyet)
                            ->first();
        if ($bieuQuyet ==  null) {
            return response()->json([
                'error_code' => 1,
                'message' => "Không tồn tại biểu quyết đang biểu quyết"
            ], Response::HTTP_OK);
        } else {
            $currentUser = Auth::user();
            $daibieuKyHop = DaiBieuKyHop::where('user_id', $currentUser->id)
                                        ->where('kyhop_id', $bieuQuyet->kyhop_id)
                                        ->first();
            if ($daibieuKyHop == null || $daibieuKyHop->vai_tro == VaiTroEnum::KhachMoi) {
                return response()->json([
                    'error_code' => 1,
                    'message' => "Bạn không có quyền biểu quyết"
                ], Response::HTTP_OK);
            }
            $tg_batdau = Carbon::parse($bieuQuyet->thoigian_batdau);
            $tg_bieuquyet = $bieuQuyet->thoigian_bieuquyet;
            $tg_expired = $tg_batdau->addMinutes($tg_bieuquyet);
            if (Carbon::now()->gt($tg_expired))
            {
                return response()->json([
                    'error_code' => 1,
                    'message' => "Hết thời gian biểu quyết"
                ], Response::HTTP_OK);
            } else {
                $data = [
                    'bieuquyetid' => strval($bieuQuyet->id),
                    'tenbieuquyet' => $bieuQuyet->ten_bieuquyet,
                    'thoigian_batdau' => $bieuQuyet->thoigian_batdau,
                    'thoigian_bieuquyet' => $bieuQuyet->thoigian_bieuquyet,
                    'thoigian_hientai' => date("Y-m-d H:i:s")
                ];
                return response()->json([
                    'error_code' => 0,
                    'data' => $data
                ], Response::HTTP_OK);
            }
        }
    }

    public function setBieuQuyet(Request $request)
    {
        $bieuquyet_id = $request->bieuquyet_id;
        $ketqua = $request->ketqua;
        if ($ketqua != GiaTriBieuQuyetEnum::DongY && $ketqua != GiaTriBieuQuyetEnum::KhongDongY
            && $ketqua != GiaTriBieuQuyetEnum::KhongBieuQuyet) {
            return response()->json([
                'error_code' => 1,
                'message' => "Giá trị bình chọn biểu quyết không hợp lệ"
            ], Response::HTTP_OK);
        }

        $bieuQuyet = BieuQuyet::find($bieuquyet_id);
        if ($bieuQuyet ==  null) {
            return response()->json([
                'error_code' => 1,
                'message' => "Biểu quyết không tồn tại"
            ], Response::HTTP_OK);
        } else {
            $currentUser = Auth::user();
            $daibieuKyHop = DaiBieuKyHop::where('user_id', $currentUser->id)
                                        ->where('kyhop_id', $bieuQuyet->kyhop_id)
                                        ->first();
            if ($daibieuKyHop == null || $daibieuKyHop->vai_tro == VaiTroEnum::KhachMoi) {
                return response()->json([
                    'error_code' => 1,
                    'message' => "Bạn không có quyền biểu quyết"
                ], Response::HTTP_OK);
            }
            if ($bieuQuyet->trang_thai != TrangThaiBieuQuyetEnum::DangBieuQuyet) {
                return response()->json([
                    'error_code' => 1,
                    'message' => "Biểu quyết đang không diễn ra"
                ], Response::HTTP_OK);
            }
            $tg_batdau = Carbon::parse($bieuQuyet->thoigian_batdau);
            $tg_bieuquyet = $bieuQuyet->thoigian_bieuquyet;
            $tg_expired = $tg_batdau->addMinutes($tg_bieuquyet);
            if (Carbon::now()->gt($tg_expired))
            {
                return response()->json([
                    'error_code' => 1,
                    'message' => "Hết thời gian biểu quyết"
                ], Response::HTTP_OK);
            }
            else
            {
                $currentUser = Auth::user();
                //Cập nhật kết quả vào database
                DB::beginTransaction();
                try {
                    $kqChitiet = KetquaBieuquyetChitiet::where('bieuquyet_id', $bieuquyet_id)
                                                    ->where('user_id', $currentUser->id)
                                                    ->first();
                    if ($kqChitiet != null) {
                        DB::table('ketqua_bieuquyet_chitiet')
                        ->where('id', $kqChitiet->id)
                        ->update([
                            'gia_tri' => $ketqua,
                            'thoigian_bieuquyet' => date("Y-m-d H:i:s")
                        ]);
                    }
                    else {
                        DB::table('ketqua_bieuquyet_chitiet')
                        ->insert([
                            'id' => 0,
                            'bieuquyet_id' => $bieuquyet_id,
                            'user_id' => $currentUser->id,
                            'gia_tri' => $ketqua,
                            'thoigian_bieuquyet' => date("Y-m-d H:i:s")
                        ]);
                    }
                    DB::commit();
                    if (setting('site.usefirebase') == "1") {
                        FireBaseHelper::SendEventBinhChonBieuQuyet($currentUser->tenant_id, $bieuQuyet->kyhop_id, $bieuquyet_id, $currentUser->id, $ketqua);
                    }
                    return response()->json([
                        'error_code' => 0,
                        'message' => 'Lưu thành công'
                    ], 200);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'error_code' => 1,
                        'message' => 'Lưu không thành công',
                        'data' => json_encode($e)
                    ], 200);
                }
            }
        }
    }

    public function getTime(Request $request) {
        return Carbon::now()->format("Y-m-d H:i:s");
    }
}
