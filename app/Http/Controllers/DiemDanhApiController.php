<?php

namespace App\Http\Controllers;

use App\Enums\BuoiHopEnum;
use App\Enums\TrangThaiDiemDanhEnum;
use App\Enums\TrangThaiKyHopEnum;
use App\Enums\VaiTroEnum;
use App\Helpers\FireBaseHelper;
use App\Helpers\LocationHelper;
use App\Models\DaiBieuKyHop;
use App\Models\LichHop;
use App\Models\DiemDanh;
use http\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class DiemDanhApiController extends Controller
{

    public $successStatus = Response::HTTP_OK;

    public function SetDiemDanh(Request $request)
    {
        try {
            $currentUser = Auth::user();
            $lat =  $request->lat;
            $long = $request->long;
            if ($lat != 0 && $long != 0) {
                //Lấy buổi họp theo userId & thời gian hiện tại
                $lsVaiTroDaiBieu = DaiBieuKyHop::where('user_id', $currentUser->id)
                        ->where(function($q) {
                            $q->where('vai_tro', VaiTroEnum::DaiBieu)
                            ->orWhere('vai_tro', VaiTroEnum::ChuToa);
                        })
                        ->whereHas('kyhop', function ($query) {
                            return $query->where('trang_thai', '=', TrangThaiKyHopEnum::DangDienRa);
                        })
                        ->first();
                if ($lsVaiTroDaiBieu != null) {
                    //Lấy lat long theo phòng họp Id
                    $buoihop = -1;
                    if (date('H') >= 6 && date('H') <= 12) {
                        $buoihop = BuoiHopEnum::BuoiSang;
                    } else if (date('H') >= 12 && date('H') <= 18) {
                        $buoihop = BuoiHopEnum::BuoiChieu;
                    }
                    $buoihopItem = LichHop::where('kyhop_id', $lsVaiTroDaiBieu->kyhop->id)
                                        ->where('buoi_hop', $buoihop)
                                        ->whereHas('thoi_gian', function ($query) {
                                            return $query->where('ngay_dienra', '=', date('Y-m-d'));
                                        })
                                        ->first();
                    if ($buoihopItem != null) {
                        $latPhongHop = $buoihopItem->phong_hop->lat;
                        $longPhongHop = $buoihopItem->phong_hop->long;
                        $distance = LocationHelper::CalculatorDistance($lat, $long, $latPhongHop, $longPhongHop);
                        $maxDistance = $buoihopItem->phong_hop->khoang_cach ?? 1000;//setting('site.maxDistance') ?? 1000;
                        if ($distance > $maxDistance) {
                            return response()->json([
                                'error_code' => 1,
                                'message' => "Vị trí ngoài phạm vi cho phép"
                            ], Response::HTTP_OK);
                        } else {
                            //Cập nhật trạng thái điểm danh vào database
                            $diemDanh = DiemDanh::where('lichhop_id', $buoihopItem->id)
                                                ->where('user_id', $currentUser->id)
                                                ->first();
                            if ($diemDanh != null && $diemDanh->trang_thai == TrangThaiDiemDanhEnum::VangMat) {
                                return response()->json([
                                    'error_code' => 1,
                                    'message' => "Bạn đã được ghi nhận vắng mặt, xin vui lòng liên hệ quản trị viên"
                                ], Response::HTTP_OK);
                            } else if ($diemDanh != null && ($diemDanh->trang_thai == TrangThaiDiemDanhEnum::DiemDanhBangApp
                                || $diemDanh->trang_thai == TrangThaiDiemDanhEnum::DiemDanhThuCong)) {
                                    return response()->json([
                                        'error_code' => 1,
                                        'message' => "Bạn đã được ghi nhận điểm danh rồi"
                                    ], Response::HTTP_OK);
                            } else {
                                DB::beginTransaction();
                                DB::table('diem_danh')->insert([
                                    'id' => 0,
                                    'lichhop_id' => $buoihopItem->id,
                                    'user_id' => $currentUser->id,
                                    'trang_thai' => TrangThaiDiemDanhEnum::DiemDanhBangApp,
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => null,
                                    'deleted_at' => null,
                                    'created_by' => $currentUser->id,
                                    'updated_by' => null,
                                    'deleted_by' => null
                                ]);
                                DB::commit();
                                //Gửi event lên firebase
                                if (setting('site.usefirebase') == "1") {
                                    FireBaseHelper::SendEventDiemDanh($currentUser->tenant_id, $buoihopItem->id, $currentUser->id, TrangThaiDiemDanhEnum::DiemDanhBangApp);
                                }
                                return response()->json([
                                    'error_code' => 0,
                                    'message' => "Điểm danh thành công"
                                ], Response::HTTP_OK);
                            }
                        }
                    }
                    else {
                        return response()->json([
                            'error_code' => 1,
                            'message' => "Không có buổi họp thỏa mãn cần điểm danh"
                        ], Response::HTTP_OK);
                    }
                } else {
                    return response()->json([
                        'error_code' => 1,
                        'message' => "Không có kỳ họp thỏa mãn cần điểm danh"
                    ], Response::HTTP_OK);
                }
            }
            else {
                return response()->json([
                    'error_code' => 1,
                    'message' => "Không có dữ liệu vị trí"
                ], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Điểm danh không thành công',
                'data' => json_encode($e),
            ], Response::HTTP_OK);
        }
    }
}
