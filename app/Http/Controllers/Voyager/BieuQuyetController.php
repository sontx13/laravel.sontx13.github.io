<?php

namespace App\Http\Controllers\Voyager;

use App\Enums\BuoiHopEnum;
use App\Enums\GiaTriBieuQuyetEnum;
use App\Enums\TrangThaiBieuQuyetEnum;
use App\Enums\TrangThaiHoatDongEnum;
use App\Enums\VaiTroEnum;
use App\Helpers\FireBaseHelper;
use App\Helpers\ValidateHelper;
use App\Models\BieuQuyet;
use App\Models\DaiBieuKyHop;
use App\Models\KetquaBieuquyetChitiet;
use App\Models\KhoaHop;
use App\Models\LichHop;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BieuQuyetController extends VoyagerBaseController
{
    // View Index Biểu quyết
    public function Index(Request $request)
    {
        $this->authorize('browse_bieu-quyet');
        $lsKhoahop = KhoaHop::where('trang_thai', TrangThaiHoatDongEnum::HoatDong)
            ->get();
        $view = 'voyager::bieu-quyet.index';
        return Voyager::view($view,compact('lsKhoahop'));
    }

    public function PartialViewDsBieuQuyet(Request $request) {
        $kyhopId = $request->kyhopid;
        $dsBieuQuyet = BieuQuyet::where('kyhop_id', $kyhopId)
                                ->orderByRaw("FIELD(trang_thai , '1', '0', '2') ASC")
                                ->orderBy('stt')
                                ->with('ketqua')
                                ->get();
        $view = 'voyager::bieu-quyet.partials.danh-sach';
        return Voyager::view($view,compact('dsBieuQuyet'));
    }

    public function ThemMoiBieuQuyet(Request $request) {
        $this->authorize('add_bieu-quyet');
        $kyhopId = $request->kyhopid;
        $bieuquyet = $request->bieuquyet;
        $thoigian = $request->thoigian;
        $stt = $request->stt;
        $currentUserId = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::table('bieu_quyet')->insert([
                'id' => 0,
                'ten_bieuquyet' => $bieuquyet,
                'thoigian_bieuquyet' => $thoigian,
                'trang_thai' => TrangThaiBieuQuyetEnum::ChuaBieuQuyet,
                'stt' => $stt,
                'thoigian_batdau' => null,
                'thoigian_ketthuc' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
                'deleted_at' => null,
                'created_by' => $currentUserId,
                'updated_by' => null,
                'deleted_by' => null,
                'kyhop_id' => $kyhopId
            ]);
            DB::commit();
            return response()->json([
                'error_code' => '0',
                'message' => 'Lưu thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => '1',
                'message' => 'Lưu không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }

    public function CapNhatBieuQuyet(Request $request) {
        $this->authorize('edit_bieu-quyet');
        $id = $request->id;
        $bieuquyet = $request->bieuquyet;
        $thoigian = $request->thoigian;
        $stt = $request->stt;
        $currentUserId = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::table('bieu_quyet')
            ->where('id', $id)
            ->update([
                'ten_bieuquyet' => $bieuquyet,
                'thoigian_bieuquyet' => $thoigian,
                'stt' => $stt,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUserId
            ]);
            DB::commit();
            return response()->json([
                'error_code' => '0',
                'message' => 'Lưu thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => '1',
                'message' => 'Lưu không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }

    public function XoaBieuQuyet(Request $request) {
        $this->authorize('delete_bieu-quyet');
        $id = $request->id;
        $currentUserId = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::table('bieu_quyet')
            ->where('id', $id)
            ->update([
                'deleted_at' => date("Y-m-d H:i:s"),
                'deleted_by' => $currentUserId
            ]);
            DB::commit();
            return response()->json([
                'error_code' => '0',
                'message' => 'Lưu thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => '1',
                'message' => 'Lưu không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }

    public function BatDauBieuQuyet(Request $request) {
        $this->authorize('edit_bieu-quyet');
        $id = $request->id;
        $kyhopid = $request->kyhopid;
        $currentUser = Auth::user();
        // Kiểm tra có biểu quyết nào đang được biểu quyết hay không
        $dangBieuQuyet = BieuQuyet::where('kyhop_id', $kyhopid)
                                ->where('trang_thai', TrangThaiBieuQuyetEnum::DangBieuQuyet)
                                ->get();
        if (count($dangBieuQuyet) > 0) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Tồn tại '.count($dangBieuQuyet). ' biểu quyết trong kỳ họp đang biểu quyết'
            ], 200);
        }
        DB::beginTransaction();
        try {
            $thoigian_batdau = date("Y-m-d H:i:s");
            $bieuquyet = BieuQuyet::find($id);
            DB::table('bieu_quyet')
            ->where('id', $id)
            ->update([
                'thoigian_batdau' => $thoigian_batdau,
                'trang_thai' => TrangThaiBieuQuyetEnum::DangBieuQuyet,
                'updated_at' => $thoigian_batdau,
                'updated_by' => $currentUser->id
            ]);
            DB::commit();
            // Gửi event lên firebase
            if (setting('site.usefirebase') == "1") {
                FireBaseHelper::SendEventBatDauBieuQuyet($currentUser->tenant_id, $kyhopid, $id, $bieuquyet->ten_bieuquyet, $thoigian_batdau, $bieuquyet->thoigian_bieuquyet);
            }
            return response()->json([
                'error_code' => '0',
                'message' => 'Lưu thành công',
                'data' => $bieuquyet
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => '1',
                'message' => 'Lưu không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }


    public function KetThucBieuQuyet(Request $request) {
        $this->authorize('edit_bieu-quyet');
        $id = $request->id;
        $currentUser = Auth::user();
        $bieuquyet = BieuQuyet::find($id);
        if ($bieuquyet == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Không tồn tại biểu quyết'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('bieu_quyet')
            ->where('id', $id)
            ->update([
                'thoigian_ketthuc' => date("Y-m-d H:i:s"),
                'trang_thai' => TrangThaiBieuQuyetEnum::DaBieuQuyet,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUser->id
            ]);
            // tính kết quả biểu quyết
            $this->InsertKetQuaBieuQuyet($bieuquyet->kyhop_id, $id);
            DB::commit();
            if (setting('site.usefirebase') == "1") {
                FireBaseHelper::SendEventKetThucBieuQuyet($currentUser->tenant_id, $bieuquyet->kyhop_id);
            }
            return response()->json([
                'error_code' => '0',
                'message' => 'Lưu thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => '1',
                'message' => 'Lưu không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }

    //Partial view trình chiếu biểu quyết
    public function PartialViewTrinhChieu(Request $request) {
        $currentUser = Auth::user();
        $tenantId = $currentUser->tenant_id;
        $thoigian_batdau = "";
        $thoigian_bieuquyet = 0;
        $id = $request->id;
        $kyhopid = $request->kyhopid;
        $buoihopid = 0;
        $daibieus = [];
        $daibieuJson = '[]';
        $isShow = false;
        $idbieuquyet = 0;
        // Xem lại kết quả
        $tongso_diemdanh = 0;
        $tongso_daibieu = 0;
        $tongso_dongy = 0;
        $tongso_khongdongy = 0;
        $tongso_khongbieuquyet = 0;
        $tyle_thamgia = 0;
        $tyle_dongy = 0;
        $tyle_khongdongy = 0;
        $tyle_khongbieuquyet = 0;
        if ($id > 0) {
            $bieuquyet = BieuQuyet::find($id);
            if ($bieuquyet != null && $bieuquyet->ketqua != null) {
                $tongso_diemdanh = $bieuquyet->ketqua->tongso_diemdanh;
                $tongso_daibieu = $bieuquyet->ketqua->tongso_daibieu;
                $tongso_dongy = $bieuquyet->ketqua->dongy;
                $tongso_khongdongy = $bieuquyet->ketqua->khong_dongy;
                $tongso_khongbieuquyet = $bieuquyet->ketqua->khong_bieuquyet;
                //Tính tỷ lệ
                $tyle_thamgia = $tongso_daibieu != 0 ? round($tongso_diemdanh/$tongso_daibieu * 100, 2) : 0;
                $tyle_dongy = $tongso_daibieu != 0 ? round($tongso_dongy/$tongso_daibieu * 100, 2) : 0;
                $tyle_khongdongy =  $tyle_khongdongy != 0 ? round($tongso_khongdongy/$tongso_daibieu * 100,2) : 0;
                $tyle_khongbieuquyet = $tyle_thamgia - $tyle_dongy - $tyle_khongdongy;
            }
        } else {
            $isShow = true;
            //Trình chiếu biểu quyết đang biểu quyết
            $bieuquyet = BieuQuyet::where('kyhop_id', $kyhopid)
                                    ->where('trang_thai', TrangThaiBieuQuyetEnum::DangBieuQuyet)
                                    ->first();
            if ($bieuquyet != null && $bieuquyet->id > 0) {
                $thoigian_bieuquyet = $bieuquyet->thoigian_bieuquyet;
                $thoigian_batdau = $bieuquyet->thoigian_batdau;
                $idbieuquyet = $bieuquyet->id;
                 // lấy ds đại biểu + trạng thái điểm danh + bình chọn biểu quyết
                $daibieus = $this->DsDaiBieu($kyhopid, $idbieuquyet, 0);
            }
            $daibieuJson = json_encode($daibieus);
            $lichHops = LichHop::where('kyhop_id', $kyhopid)
                            ->whereHas('thoi_gian', function($q){
                                $q->whereNull('deleted_at');
                            })
                            ->get();
            foreach($lichHops as $buoihop) {
                $isValid = ValidateHelper::CheckThoiGianDiemDanh($buoihop->thoi_gian->ngay_dienra, $buoihop->buoi_hop);
                if ($isValid) {
                    $buoihopid = $buoihop->id;
                break;
                }
            }
        }
        $view = 'voyager::bieu-quyet.partials.trinh-chieu-bieu-quyet';
        return Voyager::view($view,compact('tongso_diemdanh', 'tongso_daibieu', 'tongso_dongy',
            'tongso_khongdongy', 'tongso_khongbieuquyet', 'tyle_thamgia',  'tyle_dongy', 'tyle_khongdongy',
            'tyle_khongbieuquyet', 'thoigian_batdau', 'thoigian_bieuquyet', 'isShow',
            'daibieuJson', 'kyhopid', 'tenantId', 'idbieuquyet', 'buoihopid'));
    }

    public function KetQuaBieuQuyet(Request $request) {
        $bieuquyetid = $request->bieuquyetid;
        $buoihopid =  $request->buoihopid;
        $kyhopid = $request->kyhopid;
        $daibieus = $this->DsDaiBieu($kyhopid, $bieuquyetid, $buoihopid);
        return response()->json([
            'error_code' => 0,
            'data' =>  $daibieus
        ], 200);
    }

    public function TongDaiBieuDaDiemDanh($kyhopid) {
        //Đã điểm danh
        $queryDiemDanh =
            "SELECT
                count(*) AS dadiemdanh
            FROM hdnd_app.diem_danh ddanh
            INNER JOIN hdnd_app.lich_hop lich
                ON ddanh.lichhop_id = lich.id
            INNER JOIN hdnd_app.thoigian_kyhop tgian
                ON lich.thoigian_kyhop_id = tgian.id
            WHERE
                lich.kyhop_id = ?
                AND (ddanh.trang_thai = 2 OR ddanh.trang_thai = 3)
                AND lich.buoi_hop = ?
                AND tgian.ngay_dienra = ?
            ";
        $buoihop = null;
        if (date('H') >= 6 && date('H') <= 12) {
            $buoihop = BuoiHopEnum::BuoiSang;
        } else if (date('H') >= 12 && date('H') <= 18) {
            $buoihop = BuoiHopEnum::BuoiChieu;
        }
        $result = DB::select($queryDiemDanh, [$kyhopid, $buoihop, date('Y-m-d')]);
        $dadiemdanh = $result[0]->dadiemdanh;
        return $dadiemdanh;
    }

    public function DsDaiBieu($kyhopid, $bieuquyetid, $buoihopid) {
        $dsDaibieu = [];
        $buoihopId = 0;
        if ($buoihopid == 0) {
            $lichHops = LichHop::where('kyhop_id', $kyhopid)
            ->whereHas('thoi_gian', function($q){
                $q->whereNull('deleted_at');
            })
            ->get();
            foreach($lichHops as $buoihop) {
                $isValid = ValidateHelper::CheckThoiGianDiemDanh($buoihop->thoi_gian->ngay_dienra, $buoihop->buoi_hop);
                if ($isValid) {
                    $buoihopId = $buoihop->id;
                    break;
                }
            }
        }
        else {
            $buoihopId = $buoihopid;
        }
        if ($buoihopId > 0) {
            $query =
                    "SELECT
                        DISTINCT
                        daibieu.user_id,
                        CASE
                            WHEN diemdanh.trang_thai = 2 OR diemdanh.trang_thai = 3 THEN true
                            ELSE false
                        END AS trangthai_diemdanh,
                        ketqua.gia_tri
                    FROM hdnd_app.daibieu_kyhop daibieu
                    LEFT JOIN hdnd_app.diem_danh diemdanh
                        ON daibieu.user_id = diemdanh.user_id
                        AND lichhop_id = ?
                    LEFT JOIN hdnd_app.ketqua_bieuquyet_chitiet ketqua
                        ON ketqua.user_id = daibieu.user_id
                        AND ketqua.bieuquyet_id = ?
                    WHERE
                        daibieu.vai_tro = 0 OR daibieu.vai_tro = 1";
            $dsDaibieu = DB::select($query, [$buoihopId, $bieuquyetid]);
        }
        return $dsDaibieu;
    }

    public function InsertKetQuaBieuQuyet($kyhopid, $bieuquyetid) {
        // lấy ds đại biểu + trạng thái điểm danh + bình chọn biểu quyết
        $daibieus = $this->DsDaiBieu($kyhopid, $bieuquyetid, 0);
        $tongso_daibieu =  count($daibieus);
        $tongso_diemdanh = 0;
        $tongso_dongy = 0;
        $tongso_khongdongy = 0;
        $tongso_khongbieuquyet = 0;
        foreach ($daibieus as $daibieu) {
            // trạng thái điểm danh
            if ($daibieu->trangthai_diemdanh == 1) {
                $tongso_diemdanh++;
            }
            // đồng ý
            if ($daibieu->gia_tri == GiaTriBieuQuyetEnum::DongY) {
                $tongso_dongy++;
            }
            else if ($daibieu->gia_tri == GiaTriBieuQuyetEnum::KhongDongY) {
                $tongso_khongdongy++;
            }
            else if ($daibieu->gia_tri == GiaTriBieuQuyetEnum::KhongBieuQuyet){
                $tongso_khongbieuquyet++;
            }
        }
        DB::table('ketqua_bieuquyet')
        ->insert([
            'id' => 0,
            'bieuquyet_id' => $bieuquyetid,
            'dongy' => $tongso_dongy,
            'khong_dongy' => $tongso_khongdongy,
            'khong_bieuquyet' => $tongso_diemdanh - $tongso_dongy - $tongso_khongdongy,
            'tongso_daibieu' => $tongso_daibieu,
            'tongso_diemdanh' => $tongso_diemdanh
        ]);
    }

    public static function GetDisplayTTBieuQuyet($trangthai) {
        $result = "";
        switch($trangthai) {
            case 0:
                $result = "Chưa biểu quyết";
                break;
            case 1:
                $result = "Đang biểu quyết";
                break;
            case 2:
                $result = "Đã biểu quyết";
                break;
        }
        return $result;
    }
    protected function guard()
    {
        return Auth::guard(app('VoyagerGuard'));
    }
}
