<?php

namespace App\Http\Controllers;

use App\Enums\TrangThaiKhoaHopEnum;
use App\Enums\TrangThaiKyHopEnum;
use App\Models\KhoaHop;
use App\Models\KyHop;
use App\Models\ThoiGianKyHop;
use http\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class KyHopApiController extends Controller
{

    public $successStatus = Response::HTTP_OK;

    public function GetDsKyHops(Request $request)
    {
        try {
            $khoahop = KhoaHop::where('trang_thai', TrangThaiKhoaHopEnum::HoatDong)
                                ->first();
            $kyhops = KyHop::where('khoa_hop_id', $khoahop->id)
                                ->where(function($q) {
                                    $q->where('trang_thai', TrangThaiKyHopEnum::DaDienRa)
                                    ->orWhere('trang_thai', TrangThaiKyHopEnum::DangDienRa);
                                })
                                ->orderByRaw('FIELD(trang_thai, 1 , 2)')
                                ->orderBy('created_at')
                                ->get();
            return response()->json([
                'error_code' => 0,
                'data' => $kyhops,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 1,
                'message' => json_encode($e),
            ], Response::HTTP_OK);
        }
    }

    // // public function GetDsLichHops(Request $request) {
    // //     try {
    // //         $kyhop = KyHop::where('trang_thai', TrangThaiKyHopEnum::DangDienRa)
    // //                     ->first();
    // //         if ($kyhop == null) {
    // //             return response()->json([
    // //                 'error_code' => 1,
    // //                 'message' => 'Không có kỳ họp nào đang diễn ra',
    // //             ], Response::HTTP_OK);
    // //         }
    // //         $query =
    // //             "SELECT
    // //                 lich.id,
    // //                 lich.buoi_hop,
    // //                 lich.phonghop_id,
    // //                 phong.ten_phonghop,
    // //                 phong.dia_diem,
    // //                 lich.noi_dung,
    // //                 lich.thoigian_batdau,
    // //                 lich.thoigian_ketthuc,
    // //                 tgian.ngay_dienra
    // //             FROM hdnd_app.lich_hop lich
    // //             INNER JOIN hdnd_app.thoigian_kyhop tgian
    // //                 ON lich.thoigian_kyhop_id = tgian.id
    // //             INNER JOIN hdnd_app.phong_hop phong
    // //                 ON lich.phonghop_id = phong.id
    // //             WHERE
    // //                 lich.phonghop_id != 0
    // //                 AND lich.kyhop_id = ?
    // //             ORDER BY
    // //                 tgian.ngay_dienra,
    // //                 buoi_hop;";
    // //         $lichops = DB::select($query, [$kyhop->id]);
    // //         return response()->json([
    // //             'error_code' => 0,
    // //             'message' => 'Thành công',
    // //             'data' => $lichops,
    // //         ], Response::HTTP_OK);
    // //     } catch (\Exception $e) {
    // //         return response()->json([
    // //             'error_code' => 1,
    // //             'message' => json_encode($e),
    // //         ], Response::HTTP_OK);
    // //     }
    // // }

    public function GetDsLichHops(Request $request) {
        try {
            $kyhop = KyHop::where('trang_thai', TrangThaiKyHopEnum::DangDienRa)
                        ->first();
            if ($kyhop == null) {
                return response()->json([
                    'error_code' => 1,
                    'message' => 'Không có kỳ họp nào đang diễn ra',
                ], Response::HTTP_OK);
            }
            $lichops = ThoiGianKyHop::where('kyhop_id', $kyhop->id)
                                ->with('lich_hop')
                                ->whereHas('lich_hop')
                                ->select(['id', 'ngay_dienra'])
                                ->get();
            return response()->json([
                'error_code' => 0,
                'message' => 'Thành công',
                'data' => $lichops,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 1,
                'message' => json_encode($e),
            ], Response::HTTP_OK);
        }
    }
}
