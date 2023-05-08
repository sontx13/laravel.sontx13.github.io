<?php

namespace App\Http\Controllers\Voyager;

use App\Enums\BuoiHopEnum;
use App\Enums\TrangThaiDiemDanhEnum;
use App\Enums\TrangThaiKhoaHopEnum;
use App\Enums\TrangThaiKyHopEnum;
use App\Enums\VaiTroEnum;
use App\Helpers\FireBaseHelper;
use App\Helpers\ValidateHelper;
use App\Models\DaiBieuKyHop;
use App\Models\DiemDanh;
use App\Models\KhoaHop;
use App\Models\KyHop;
use App\Models\LichHop;
use App\Models\User;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiemDanhController extends VoyagerBaseController
{
    // View Index Điểm danh
    public function Index(Request $request)
    {
        $this->authorize('browse_diem-danh');
        $lsKhoahop = KhoaHop::where('trang_thai', TrangThaiKhoaHopEnum::HoatDong)
            ->get();
        $currentUser = Auth::user();
        $tenantId = $currentUser->tenant_id;
        $view = 'voyager::diem-danh.index';
        return Voyager::view($view, compact('lsKhoahop', 'tenantId'));
    }

    // Partial view thêm mới kỳ họp
    public function PartialViewDsBuoiHop(Request $request)
    {
        $kyhopId = $request->kyhopid;
        $query =
            "SELECT
                lich.id,
                lich.buoi_hop,
                lich.phonghop_id,
                thgian.ngay_dienra,
                thgian.kyhop_id
            FROM hdnd_app.thoigian_kyhop thgian
            INNER JOIN hdnd_app.lich_hop lich
                ON thgian.kyhop_id = lich.kyhop_id
                AND thgian.id = lich.thoigian_kyhop_id
            WHERE
                thgian.kyhop_id = ?
                AND thgian.deleted_at IS NULl
                AND lich.phonghop_id <> 0
            ORDER BY
                thgian.ngay_dienra,
                lich.buoi_hop";
        $lsBuoiHops = DB::select($query, [$kyhopId]);
        $view = 'voyager::diem-danh.partials.buoi-hop';
        return Voyager::view($view,compact('lsBuoiHops'));
    }

    // Partial view thêm mới kỳ họp
    public function PartialViewDiemDanh(Request $request)
    {
        $lichhopId = $request->lichhopid;
        $query =
            "SELECT
                u.id,
                u.name,
                diemdanh.id as diemdanh_id,
                diemdanh.trang_thai
            FROM hdnd_app.daibieu_kyhop daibieu
            INNER JOIN hdnd_app.lich_hop lich
                ON daibieu.kyhop_id = lich.kyhop_id
            LEFT JOIN hdnd_app.diem_danh diemdanh
                ON lich.id = diemdanh.lichhop_id
                AND diemdanh.user_id = daibieu.user_id
            LEFT JOIN hdnd_app.users u
                ON daibieu.user_id = u.id
            WHERE
                lich.id = ?
                AND daibieu.vai_tro IN (0,1)
            ORDER BY
                daibieu.stt";
        $lsDiemDanhs = DB::select($query, [$lichhopId]);
        $view = 'voyager::diem-danh.partials.diem-danh';
        return Voyager::view($view,compact('lsDiemDanhs'));
    }

    // Lưu thủ công
    public function LuuThuCong(Request $request)
    {
        $this->authorize('edit_diem-danh');
        $currentUser = Auth::user();
        $lichhopId = $request->lichhopid;
        $lsdaibieus = json_decode($request->lsdaibieus);
        DB::beginTransaction();
        try {
            foreach ($lsdaibieus as $daibieu) {
                $count = DiemDanh::where('lichhop_id', $lichhopId)
                                ->where("user_id", $daibieu->userId)
                                ->count();
                if ($count > 0) {
                    //Update
                    DB::table('diem_danh')
                    ->where('lichhop_id', $lichhopId)
                    ->where('user_id', $daibieu->userId)
                    ->update([
                        'trang_thai' => $daibieu->trangThai,
                        'updated_at' => date("Y-m-d H:i:s"),
                        'updated_by' => 0
                    ]);
                } else {
                    //Insert
                    DB::table('diem_danh')->insert([
                        'id' => 0,
                        'lichhop_id' => $lichhopId,
                        'user_id' => $daibieu->userId,
                        'trang_thai' => $daibieu->trangThai,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => null,
                        'deleted_at' => null,
                        'created_by' => 0,
                        'updated_by' => null,
                        'deleted_by' => null
                    ]);
                }
                if (setting('site.usefirebase') == "1") {
                    FireBaseHelper::SendEventDiemDanh($currentUser->tenant_id, $lichhopId , $daibieu->userId, $daibieu->trangThai);
                }
            }
            DB::commit();
            return response()->json([
                'error_code' => '0',
                'message' => 'Điểm danh thành công',
                'data' =>$count
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => '1',
                'message' => 'Điểm danh không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }

    //Partial view trình chiếu điểm danh
    public function PartialViewTrinhChieu(Request $request) {
        $kyhopId = $request->kyhopid;
        $kyhop = KyHop::find($kyhopId);
        $lichHops = LichHop::where('kyhop_id', $kyhopId)
                            ->whereHas('thoi_gian', function($q){
                                $q->whereNull('deleted_at');
                            })
                            ->get();
        $isValid = false;
        $buoihopId = 0;
        foreach($lichHops as $buoihop) {
            $isValid = ValidateHelper::CheckThoiGianDiemDanh($buoihop->thoi_gian->ngay_dienra, $buoihop->buoi_hop);
            if ($isValid) {
                $buoihopId = $buoihop->id;
                break;
            }
        }
        $lsDaiBieu = [];
        if ($isValid) {
            //Lấy ds đại biểu đã ghi nhận điểm danh, vắng mặt
            $lsDaiBieu = DiemDanh::where('lichhop_id', $buoihopId)
                        ->where(function($q) {
                            $q->where('trang_thai', TrangThaiDiemDanhEnum::DiemDanhBangApp)
                            ->orWhere('trang_thai', TrangThaiDiemDanhEnum::DiemDanhThuCong);
                        })
                        ->select('user_id','trang_thai')
                        ->get();
        }
        $lsDaiBieuJson = json_encode($lsDaiBieu);
        //Tổng số đại biểu
        $toltalDB = DaiBieuKyHop::where('kyhop_id', $kyhopId)
                                ->where(function($q) {
                                    $q->where('vai_tro', VaiTroEnum::DaiBieu)
                                    ->orWhere('vai_tro', VaiTroEnum::ChuToa);
                                })
                                ->count();
        $tenantId = Auth::user()->tenant_id;
        $view = 'voyager::diem-danh.partials.trinh-chieu-diem-danh';
        return Voyager::view($view,compact('kyhop', 'lsDaiBieuJson', 'toltalDB', 'isValid', 'tenantId', 'buoihopId'));
    }

    // Danh sách điểm danh
    public function DanhSachDiemDanh(Request $request) {
        $lichopid = $request->lichhopid;
        //Lấy ds đại biểu đã ghi nhận điểm danh, vắng mặt
        $lsDaiBieu = DiemDanh::where('lichhop_id', $lichopid)
        ->where(function($q) {
            $q->where('trang_thai', TrangThaiDiemDanhEnum::DiemDanhBangApp)
            ->orWhere('trang_thai', TrangThaiDiemDanhEnum::DiemDanhThuCong);
        })
        ->select('user_id','trang_thai')
        ->get();
        return response()->json([
            'error_code' => 0,
            'message' => 'Danh sách điểm danh',
            'data' => $lsDaiBieu
        ], 200);
    }

    public static function GetDisplayBuoiHop($buoihop) {
        $result = "";
        if ($buoihop == 0) {
            $result = "Sáng";
        } else {
            $result = "Chiều";
        }
        return $result;
    }

    public static function GetDisplayTrangThaiDiemDanh($trangthai) {
        $result = "";
        if ($trangthai == 1 || $trangthai == 2) {
            $result = "Đã điểm danh";
        } else {
            $result = "Chưa điểm danh";
        }
        return $result;
    }
    protected function guard()
    {
        return Auth::guard(app('VoyagerGuard'));
    }
}
