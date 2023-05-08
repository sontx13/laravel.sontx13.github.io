<?php

namespace App\Http\Controllers\Voyager;

use App\Enums\BuoiHopEnum;
use App\Enums\TrangThaiKhoaHopEnum;
use App\Enums\TrangThaiKyHopEnum;
use App\Enums\VaiTroEnum;
use App\Models\DaibieuKhoahop;
use App\Models\DaiBieuKyHop;
use App\Models\KhoaHop;
use App\Models\KyHop;
use App\Models\LichHop;
use App\Models\PhongHop;
use App\Models\ThoiGianKyHop;
use App\Models\User;
use DateTime;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KyHopController extends VoyagerBaseController
{
    // View Ds kỳ họp
    public function Index(Request $request)
    {
        $this->authorize('browse_ky-hop');
        $lsKhoahop = KhoaHop::where('trang_thai', TrangThaiKhoaHopEnum::HoatDong)
            ->get();
        $view = 'voyager::ky-hop.index';
        return Voyager::view($view,compact('lsKhoahop'));
    }

    // Partial view danh sách kỳ họp
    public function PartialViewDsKyHop(Request $request)
    {
        $khoahopid = $request->khoahopid;
        $lsKyHops = KyHop::where('khoa_hop_id', $khoahopid)
                        ->get();
        $view = 'voyager::ky-hop.partials.danh-sach';
        return Voyager::view($view,compact('lsKyHops'));
    }

    // Lấy trạng thái kỳ họp
    public static function GetDisplayTTKyHop($trangthai) {
        $result = "";
        switch($trangthai) {
            case 0:
                $result = "Không sử dụng";
                break;
            case 1:
                $result = "Sắp diễn ra";
                break;
            case 2:
                $result = "Đã diễn ra";
                break;
        }
        return $result;
    }
    // View thêm mới kỳ họp
    public function ViewCreate(Request $request)
    {
        $khoahopId = $request->khoahopid;
        // $lsUsers = User::get();
        $thoiGianKyHop='';
        $kyHop='';

        $lsUsers = User::get();
        $query =
            "SELECT
                u.*,
                dbkyhop.user_id,
                dbkyhop.kyhop_id,
                dbkyhop.vai_tro,
                dbkyhop.quyen_bieuquyet
            FROM hdnd_app.users u
            LEFT JOIN hdnd_app.daibieu_kyhop dbkyhop
                ON u.id = dbkyhop.user_id;";
        $lsUserVaiTros = DB::select($query);
        $view = 'voyager::ky-hop.edit-add';
        return Voyager::view($view,compact('khoahopId', 'isCreate', 'lsUserVaiTros','thoiGianKyHop','kyHop'));
        //KyHop::where('user_id', '=' , 1)->get()
    }

    // Action thêm mới kỳ họp
    public function CreateKyHop(Request $request) {
        $this->authorize('add_ky-hop');
        $currentUser = Auth::user();
        $khoahopid = $request->khoahopid;
        $tenkyhop = $request->tenkyhop;
        $ngayhop = $request->ngayhop;
        $diadiem = $request->diadiem;
        $trangthai = $request->trangthai;
        $khoahop = KhoaHop::find($khoahopid);
        if ($khoahop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Khóa họp không tồn tại'
            ], 200);
        }
        if ($tenkyhop == null || $tenkyhop == "") {
            return response()->json([
                'error_code' => 1,
                'message' => 'Tên kỳ họp không được để trống'
            ], 200);
        }
        if ($ngayhop == null || $ngayhop == "") {
            return response()->json([
                'error_code' => 1,
                'message' => 'Ngày họp không được để trống'
            ], 200);
        }
        $dsNgayHop = explode(",", $ngayhop);
        array_unique($dsNgayHop);
        sort($dsNgayHop);
        DB::beginTransaction();
        try {
            $kyhopid = DB::table('ky_hop')
            ->insertGetId([
                'id' => 0,
                'ten_ky_hop' => $tenkyhop,
                'khoa_hop_id' => $khoahopid,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
                'created_by' => $currentUser->id,
                'deleted_at' => null,
                'deleted_by' => null,
                'trang_thai' => $trangthai,
                'tenant_id' => $currentUser->tenant_id,
                'dia_diem' => $diadiem
            ]);
            foreach ($dsNgayHop as $ngayhopString) {
                $ngayhopDate = DateTime::createFromFormat('d/m/Y', $ngayhopString);
                DB::table('thoigian_kyhop')
                ->insertGetId([
                    'id' => 0,
                    'kyhop_id' => $kyhopid,
                    'ngay_dienra' => $ngayhopDate
                ]);
            }
            DB::commit();
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

    // Partial view modal sửa kỳ họp
    public function PartialViewSuaKyHop(Request $request)
    {
        $kyhopid = $request->kyhopid;
        $kyhop = KyHop::find($kyhopid);
        $dsNgayHop = array_map(function ($itemthoigian) {
            $date = DateTime::createFromFormat('Y-m-d', $itemthoigian['ngay_dienra']);
            return $date->format('d/m/Y');
        }, $kyhop->thoigian->toArray());
        $ngayhop = implode(",", $dsNgayHop);
        $view = 'voyager::ky-hop.partials.sua-modal';
        return Voyager::view($view,compact('kyhop', 'ngayhop'));
    }

    // Action cập nhật kỳ họp
    public function UpdateKyHop(Request $request) {
        $this->authorize('edit_ky-hop');
        $currentUser = Auth::user();
        $kyhopid = $request->kyhopid;
        $tenkyhop = $request->tenkyhop;
        $ngayhop = $request->ngayhop;
        $diadiem = $request->diadiem;
        $trangthai = $request->trangthai;
        $kyhop = KyHop::find($kyhopid);
        if ($kyhop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Kỳ họp không tồn tại'
            ], 200);
        }
        if ($tenkyhop == null || $tenkyhop == "") {
            return response()->json([
                'error_code' => 1,
                'message' => 'Tên kỳ họp không được để trống'
            ], 200);
        }
        if ($ngayhop == null || $ngayhop == "") {
            return response()->json([
                'error_code' => 1,
                'message' => 'Ngày họp không được để trống'
            ], 200);
        }
        $dsNgayHop = explode(",", $ngayhop);
        array_unique($dsNgayHop);
        $dsNgayHopDate = array_map(function ($ngay) {
            $date = DateTime::createFromFormat('d/m/Y', $ngay);
            return $date;
        }, $dsNgayHop);
        sort($dsNgayHopDate);

        DB::beginTransaction();
        try {
            DB::table('ky_hop')
            ->where('id', $kyhopid)
            ->update([
                'ten_ky_hop' => $tenkyhop,
                'trang_thai' => $trangthai,
                'dia_diem' => $diadiem,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUser->id
            ]);
            // 1. Lấy tất cả các ngày họp đã được thiết lập trong db (bao gồm những ngày đã xóa mềm)
            $dsNgayHopAllOfDBs = ThoiGianKyHop::where('kyhop_id', $kyhopid)
                                        ->withTrashed()
                                        ->get()
                                        ->toArray();
            // Lấy danh sách ngày họp trong db mà bị bỏ đi => xóa mềm những ngày họp này đi
            $dsNgayHopNewNotContainsOld = array_filter($dsNgayHopAllOfDBs, function($obj) use ($dsNgayHopDate) {
                $ngayhopItemDate = DateTime::createFromFormat('Y-m-d', $obj['ngay_dienra']);
                if (!in_array($ngayhopItemDate, $dsNgayHopDate)) {
                    return true;
                }
                return false;
            });

            foreach ($dsNgayHopNewNotContainsOld as $itemMustDelete) {
                DB::table('thoigian_kyhop')
                ->where('id', $itemMustDelete['id'])
                ->update([
                    'deleted_at' => date("Y-m-d H:i:s"),
                    'deleted_by' => $currentUser->id
                ]);
            }

            // Lấy danh sách ngày họp trong db mà đã bị xóa => bỏ xóa mềm những ngày này đi
            $dsNgayHopOldDeleted = array_filter($dsNgayHopAllOfDBs, function($obj) use ($dsNgayHopDate) {
                $ngayhopItemDate = DateTime::createFromFormat('Y-m-d', $obj['ngay_dienra']);
                if (in_array($ngayhopItemDate, $dsNgayHopDate) && $obj['deleted_at'] != null) {
                    return true;
                }
                return false;
            });

            foreach ($dsNgayHopOldDeleted as $itemDeleted) {
                DB::table('thoigian_kyhop')
                ->where('id', $itemDeleted['id'])
                ->update([
                    'deleted_at' => null,
                    'deleted_by' => null
                ]);
            }


            $dsNgayHopDB = array_map(function ($obj) {
                $date = DateTime::createFromFormat('Y-m-d', $obj['ngay_dienra']);
                return $date;
            }, $dsNgayHopAllOfDBs);


            // Lấy danh sách ngày họp mới mà db chưa có => insert thêm vào database
            $dsNgayHopDbNotContainsNew = array_filter($dsNgayHopDate, function($obj) use ($dsNgayHopDB) {
                if (!in_array($obj, $dsNgayHopDB)) {
                    return true;
                }
                return false;
            });
            foreach ($dsNgayHopDbNotContainsNew as $itemInsert) {
                DB::table('thoigian_kyhop')
                ->insertGetId([
                    'id' => 0,
                    'kyhop_id' => $kyhopid,
                    'ngay_dienra' => $itemInsert
                ]);
            }
            DB::commit();
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
    // Action xóa kỳ họp
    public function XoaKyHop(Request $request) {
        $this->authorize('delete_ky-hop');
        $currentUser = Auth::user();
        $kyhopid = $request->kyhopid;
        $kyhop = KyHop::find($kyhopid);
        if ($kyhop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Kỳ họp không tồn tại'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('ky_hop')
            ->where('id', $kyhopid)
            ->update([
                'deleted_at' => date("Y-m-d H:i:s"),
                'deleted_by' => $currentUser->id
            ]);

            //Xóa thời gian kỳ họp
            DB::table('thoigian_kyhop')
                ->where('kyhop_id', $kyhopid)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => date("Y-m-d H:i:s"),
                    'deleted_by' => $currentUser->id
                ]);
            DB::commit();
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

    // Partial view lịch họp
    public function PartialViewSuaLichHop(Request $request) {
        $this->authorize('browse_lich-hop');
        $kyhopid = $request->kyhopid;
        $kyhop = KyHop::find($kyhopid);
        $this->SetLichHop($kyhopid);
        // $lichhops = LichHop::where('kyhop_id', $kyhopid)
        //                     ->get();
        $thoigians = ThoiGianKyHop::where('kyhop_id', $kyhopid)
                    ->orderBy('ngay_dienra')
                    ->get();
        $dmPhonghops = PhongHop::where('status', 1)
                    ->get();
        $gioBuoiSang = ['06', '07', '08', '09', '10', '11', '12'];
        $gioBuoiChieu = ['12', '13', '14', '15', '16', '17', '18'];
        $phuts = ['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55'];
        $view = 'voyager::ky-hop.partials.sua-lichhop-modal';
        return Voyager::view($view,compact('kyhop', 'thoigians', 'gioBuoiSang', 'gioBuoiChieu', 'phuts', 'dmPhonghops'));
    }

    private function SetLichHop($kyhopid) {
        $thoigian_kyhop = ThoiGianKyHop::where('kyhop_id', $kyhopid)
                                        ->orderBy('ngay_dienra')
                                        ->get();
        $currentUser = Auth::user();
        DB::beginTransaction();
        try {
            foreach ($thoigian_kyhop as $ngayhop) {
                $buoiSang = LichHop::where('thoigian_kyhop_id', $ngayhop->id)
                                    ->where('buoi_hop', BuoiHopEnum::BuoiSang)
                                    ->first();
                if ($buoiSang == null) {
                    DB::table('lich_hop')
                    ->insert([
                        'id' => 0,
                        'buoi_hop' => BuoiHopEnum::BuoiSang,
                        'phonghop_id' => 0,
                        'noi_dung' => null,
                        'thoigian_batdau' => null,
                        'thoigian_ketthuc' => null,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' =>  $currentUser->id,
                        'updated_at' => null,
                        'updated_by' => null,
                        'deleted_at' => null,
                        'deleted_by' => null,
                        'kyhop_id' => $kyhopid,
                        'thoigian_kyhop_id' => $ngayhop->id,
                    ]);
                }
                $buoiChieu = LichHop::where('thoigian_kyhop_id', $ngayhop->id)
                                    ->where('buoi_hop', BuoiHopEnum::BuoiChieu)
                                    ->first();
                if ($buoiChieu == null) {
                    DB::table('lich_hop')
                    ->insert([
                        'id' => 0,
                        'buoi_hop' => BuoiHopEnum::BuoiChieu,
                        'phonghop_id' => 0,
                        'noi_dung' => null,
                        'thoigian_batdau' => null,
                        'thoigian_ketthuc' => null,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' =>  $currentUser->id,
                        'updated_at' => null,
                        'updated_by' => null,
                        'deleted_at' => null,
                        'deleted_by' => null,
                        'kyhop_id' => $kyhopid,
                        'thoigian_kyhop_id' => $ngayhop->id,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
        }
    }
    // Action cập nhật lịch họp
    public function UpdateLichHop(Request $request) {
        $this->authorize('edit_lich-hop');
        $lichhopid = $request->lichhopid;
        $giobatdau = $request->giobatdau;
        $phutbatdau = $request->phutbatdau;
        $gioketthuc = $request->gioketthuc;
        $phutketthuc = $request->phutketthuc;
        $phonghop = $request->phonghop;
        $noidung = $request->noidung;
        $lichhop = LichHop::find($lichhopid);
        $currentUser = Auth::user();
        if ($lichhop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Lịch họp không tồn tại'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('lich_hop')
                ->where('id', $lichhopid)
                ->update([
                    'phonghop_id' => $phonghop,
                    'noi_dung' => $noidung,
                    'thoigian_batdau' => $giobatdau.$phutbatdau,
                    'thoigian_ketthuc' => $gioketthuc.$phutketthuc,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => $currentUser->id
                ]);
            DB::commit();
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
    // PartialView sửa vai trò kỳ họp
    public function PartialViewSuaVaiTroKyHop(Request $request) {
        $this->authorize('browse_daibieu-kyhop');
        $kyhopid = $request->kyhopid;
        $kyhop = KyHop::find($kyhopid);
        $daibieukhoahops = DaibieuKhoahop::where('khoahop_id', $kyhop->khoa_hop_id)
                                        ->with('user')
                                        ->get();
        $chutoas = DaiBieuKyHop::where('kyhop_id', $kyhopid)
                                ->where('vai_tro', VaiTroEnum::ChuToa)
                                ->get()
                                ->toArray();
        $chutoaids = array_map(function ($obj) {
            return $obj['user_id'];
        },  $chutoas);
        $view = 'voyager::ky-hop.partials.sua-vaitro-kyhop-modal';
        return Voyager::view($view,compact('kyhop', 'daibieukhoahops', 'chutoaids' ));
    }
    // PartialView danh sách đại biểu
    public function PartialViewDanhSachDaiBieu(Request $request) {
        $kyhopid = $request->kyhopid;
        $kyhop = KyHop::find($kyhopid);
        $daibieus = DaiBieuKyHop::where('kyhop_id', $kyhopid)
                                ->where('vai_tro', VaiTroEnum::DaiBieu)
                                ->with('user')
                                ->orderBy('stt')
                                ->get();
        $khachmois = DaiBieuKyHop::where('kyhop_id', $kyhopid)
                                ->where('vai_tro', VaiTroEnum::KhachMoi)
                                ->with('user')
                                ->orderBy('stt')
                                ->get();
        $view = 'voyager::ky-hop.partials.danh-sach-daibieu-kyhop';
        return Voyager::view($view,compact('kyhop', 'daibieus', 'khachmois' ));
    }
     // Action thêm tất cả đại biểu của khóa họp vào đại biểu kỳ họp
    public function ThemTatCaDaiBieuKhoaHop(Request $request) {
        $this->authorize('edit_daibieu-kyhop');
        $kyhopid = $request->kyhopid;
        $kyhop = KyHop::find($kyhopid);
        DB::beginTransaction();
        try {
            $query = "
                INSERT INTO hdnd_app.daibieu_kyhop
                SELECT
                    0 AS id,
                    dbkhoahop.user_id,
                    ? AS kyhop_id,
                    0 AS vai_tro,
                    1 AS quyen_bieuquyet,
                    dbkhoahop.stt AS stt
                FROM hdnd_app.daibieu_khoahop dbkhoahop
                LEFT JOIN hdnd_app.daibieu_kyhop dbkyhop
                    ON dbkyhop.user_id = dbkhoahop.user_id
                    AND dbkyhop.kyhop_id = ?
                    AND dbkhoahop.khoahop_id = ?
                WHERE
                    dbkyhop.id IS NULL;
            ";
            DB::insert($query, [$kyhopid, $kyhopid, $kyhop->khoa_hop_id]);
            DB::commit();
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
    // Action cập nhật chủ tọa
    public function CapNhatChuToa(Request $request) {
        $this->authorize('edit_daibieu-kyhop');
        $kyhopid = $request->kyhopid;
        $chutoas = $request->chutoas;
        // Xóa danh sách chủ tọa
        DB::beginTransaction();
        try {
            DB::table('daibieu_kyhop')
                    ->where('kyhop_id', $kyhopid)
                    ->where('vai_tro', VaiTroEnum::ChuToa)
                    ->update([
                        'vai_tro' => VaiTroEnum::DaiBieu,
                    ]);
            DB::commit();
            if ($chutoas == null) {
                return response()->json([
                    'error_code' => 0,
                    'message' => 'Lưu thành công'
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => 1,
                'message' => 'Lưu không thành công 1',
                'data' => json_encode($e)
            ], 200);
        }
        DB::beginTransaction();
        try {
            foreach($chutoas as $index => $chutoa) {
                $vaitroUser = DaiBieuKyHop::where('kyhop_id', $kyhopid)
                                        ->where('user_id', $chutoa)
                                        ->first();
                if ($vaitroUser == null) {
                    //Insert chủ tọa
                    DB::table('daibieu_kyhop')
                    ->insert([
                        'id' => 0,
                        'user_id' => $chutoa,
                        'kyhop_id' => $kyhopid,
                        'vai_tro' => VaiTroEnum::ChuToa,
                        'quyen_bieuquyet' => 1,
                        'stt' => $index + 1
                    ]);
                } else if ($vaitroUser != null && $vaitroUser->vai_tro == VaiTroEnum::DaiBieu){
                    DB::table('daibieu_kyhop')
                    ->where('kyhop_id', $kyhopid)
                    ->where('user_id', $chutoa)
                    ->update([
                        'vai_tro' => VaiTroEnum::ChuToa,
                    ]);
                }
            }
            DB::commit();
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
    // Action xóa vai trò đại biểu
    public function XoaVaiTroDaiBieu(Request $request) {
        $this->authorize('delete_daibieu-kyhop');
        $kyhopid = $request->kyhopid;
        $idvaitro = $request->idvaitro;
        DB::beginTransaction();
        try {
            DB::table('daibieu_kyhop')
                    ->where('kyhop_id', $kyhopid)
                    ->where('id', $idvaitro)
                    ->delete();
            DB::commit();
            return response()->json([
                'error_code' => 0,
                'message' => 'Lưu thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => 1,
                'message' => 'Lưu không thành công 1',
                'data' => json_encode($e)
            ], 200);
        }
    }

    public function PartialViewComBoBoxDaiBieu(Request $request) {
        $kyhopid = $request->kyhopid;
        $kyhop = KyHop::find($kyhopid);
        $query = "
            SELECT
                dbkhoahop.user_id,
                u.name
            FROM hdnd_app.daibieu_khoahop dbkhoahop
            LEFT JOIN hdnd_app.daibieu_kyhop dbkyhop
                ON dbkyhop.user_id = dbkhoahop.user_id
                AND dbkyhop.kyhop_id = ?
                AND dbkhoahop.khoahop_id = ?
            INNER JOIN hdnd_app.users u
                ON dbkhoahop.user_id = u.id
            WHERE
                dbkyhop.id IS NULL;
        ";
        $daibieus = DB::select($query, [$kyhopid, $kyhop->khoa_hop_id]);
        $view = 'voyager::ky-hop.partials.combobox-daibieu-kyhop';
        return Voyager::view($view,compact('kyhop', 'daibieus'));
    }

    public function ThemDaiBieu(Request $request) {
        $this->authorize('edit_daibieu-kyhop');
        $kyhopid = $request->kyhopid;
        $daibieu = $request->daibieu;
        $vaitroDaiBieu = DaiBieuKyHop::where('kyhop_id', $kyhopid)
                                    ->where('user_id', $daibieu)
                                    ->first();
        $kyhop = KyHop::find($kyhopid);
        if ($kyhop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Kỳ họp không tồn tại'
            ], 200);
        }
        $daibieuKhoaHop = DaibieuKhoahop::where('khoahop_id', $kyhop->khoahop_id)
                                        ->where('user_id', $daibieu)
                                        ->first();
        if ($daibieuKhoaHop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đại biểu khóa họp không tồn tại'
            ], 200);
        }
        if ($vaitroDaiBieu == null) {
            DB::beginTransaction();
            try {
                DB::table('daibieu_kyhop')
                        ->insert([
                            'id' => 0,
                            'user_id' =>  $daibieu,
                            'kyhop_id' => $kyhopid,
                            'vai_tro' => VaiTroEnum::DaiBieu,
                            'quyen_bieuquyet' => 1,
                            'stt' => $daibieuKhoaHop->stt
                        ]);
                DB::commit();
                return response()->json([
                    'error_code' => 0,
                    'message' => 'Lưu thành công'
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error_code' => 1,
                    'message' => 'Lưu không thành công 1',
                    'data' => json_encode($e)
                ], 200);
            }
        } else {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đại biểu đã được gắn vai trò'
            ], 200);
        }
    }

    public function PartialViewComBoBoxKhachMoi(Request $request) {
        $kyhopid = $request->kyhopid;
        $kyhop = KyHop::find($kyhopid);
        $currentUser = Auth::user();
        $query = "
            SELECT
                u.id AS user_id,
                u.name
            FROM hdnd_app.users u
            LEFT JOIN hdnd_app.daibieu_khoahop dbkhoahop
                ON dbkhoahop.user_id = u.id
                AND dbkhoahop.khoahop_id = ?
            LEFT JOIN hdnd_app.daibieu_kyhop dbkyhop
                ON u.id = dbkyhop.user_id AND dbkyhop.kyhop_id = ?
            WHERE
                u.tenant_id = ?
                AND u.is_active = 1
                AND u.is_admin = 0
                AND u.deleted_at IS NULL
                AND dbkhoahop.id IS NULL
                AND dbkyhop.id IS NULL;
        ";
        $khachmois = DB::select($query, [$kyhop->khoa_hop_id, $kyhop->id, $currentUser->tenant_id]);
        $view = 'voyager::ky-hop.partials.combobox-khachmoi-kyhop';
        return Voyager::view($view,compact('kyhop', 'khachmois'));
    }

    public function ThemKhachMoi(Request $request) {
        $this->authorize('edit_daibieu-kyhop');
        $kyhopid = $request->kyhopid;
        $khachmoi = $request->khachmoi;
        $vaitroKhachMoi = DaiBieuKyHop::where('kyhop_id', $kyhopid)
                                    ->where('user_id', $khachmoi)
                                    ->first();
        if ($vaitroKhachMoi == null) {
            DB::beginTransaction();
            try {
                DB::table('daibieu_kyhop')
                        ->insert([
                            'id' => 0,
                            'user_id' =>  $khachmoi,
                            'kyhop_id' => $kyhopid,
                            'vai_tro' => VaiTroEnum::KhachMoi,
                            'quyen_bieuquyet' => 1,
                            'stt' => null
                        ]);
                DB::commit();
                return response()->json([
                    'error_code' => 0,
                    'message' => 'Lưu thành công'
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error_code' => 1,
                    'message' => 'Lưu không thành công 1',
                    'data' => json_encode($e)
                ], 200);
            }
        } else {
            return response()->json([
                'error_code' => 1,
                'message' => 'Khách mời đã được thêm'
            ], 200);
        }
    }

    public function ReloadSTT(Request $request) {
        $this->authorize('edit_daibieu-kyhop');
        $kyhopid = $request->kyhopid;
        $kyhop = KyHop::find($kyhopid);
        if ($kyhop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Kỳ họp không tồn tại'
            ], 200);
        }
        DB::beginTransaction();
        try {
            $query = "
                UPDATE hdnd_app.daibieu_kyhop dbky
                INNER JOIN hdnd_app.daibieu_khoahop dbkhoa
                    ON dbky.user_id = dbkhoa.user_id
                    AND dbky.kyhop_id = ? AND dbkhoa.khoahop_id = ?
                SET dbky.stt = dbkhoa.stt
                WHERE
                    kyhop_id = ?";
            DB::update($query, [$kyhopid, $kyhop->khoa_hop_id, $kyhopid]);
            DB::commit();
            return response()->json([
                'error_code' => 0,
                'message' => 'Lưu thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => 1,
                'message' => 'Lưu không thành công 1',
                'data' => json_encode($e)
            ], 200);
        }
    }

    //Action GetList kỳ họp theo ID khóa họp
    public function Getlistkyhopbyidkhoahop(Request $request) {
        $idKhoaHop=$request->khoahopid;
        $listKyHop= KyHop::where('khoa_hop_id',$idKhoaHop)->get();

        $view = 'voyager::ky-hop.partials.dskyhop';
        return Voyager::view($view,compact('listKyHop'));
    }

    // Lấy danh sách kỳ họp theo khóa họp
    public function GetKyHops(Request $request) {
        $khoahopId = $request->khoahopid;
        $currentUser = Auth::user();
        $lsKyhops = KyHop::where(function ($q) use($khoahopId) {
                                if ($khoahopId != 0) {
                                    $q->where('khoa_hop_id', $khoahopId);
                                }
                            })
                            ->where(function ($q) {
                                $q->where('trang_thai', TrangThaiKyHopEnum::DangDienRa)
                                ->orWhere('trang_thai', TrangThaiKyHopEnum::DaDienRa);
                            })
                            ->orderByRaw('FIELD(trang_thai, 1 , 2)')
                            ->select(['id', 'ten_ky_hop as text'])
                            ->get();
        return response()->json([
            'error_code' => '0',
            'message' => '',
            'data' => $lsKyhops
        ], 200);
    }

    // Lấy ds đại biểu theo kỳ họp
    public function DsDaiBieus(Request $request) {
        $kyhopid = $request->kyhopid;
        $query =
        "SELECT
	        daibieu.user_id as id,
            us.name as text
        FROM hdnd_app.daibieu_kyhop daibieu
        INNER JOIN hdnd_app.users us
	        ON daibieu.user_id = us.id
        WHERE
	        daibieu.kyhop_id = ?
        AND daibieu.vai_tro IN (1,2)";
        $lsDaiBieus = DB::select($query, [$kyhopid]);
        return response()->json([
            'error_code' => '0',
            'message' => '',
            'data' => $lsDaiBieus
        ], 200);
    }

    public static function GetDisplayBuoiHop($buoihop) {
        $result = "";
        switch($buoihop) {
            case 0:
                $result = "Buổi Sáng";
                break;
            case 1:
                $result = "Buổi Chiều";
                break;
        }
        return $result;
    }
    public static function GetDisplayVaiTroDaiBieu($vaitro) {
        $result = "";
        switch($vaitro) {
            case 0:
                $result = "Đại biểu";
                break;
            case 1:
                $result = "Chủ tọa";
                break;
            case 2:
                $result = "Khách mời";
                break;
        }
        return $result;
    }
    public static function GetDisplayClassVaiTroDaiBieu($vaitro) {
        $result = "";
        switch($vaitro) {
            case 0:
                $result = "vaitro-daibieu";
                break;
            case 1:
                $result = "vaitro-chutoa";
                break;
            case 2:
                $result = "vaitro-khachmoi";
                break;
        }
        return $result;
    }
    public static function GetClassTrangThaiKyHop($trangthai) {
        $class = "";
        switch($trangthai) {
            case 0:
                $class = "tt-khongsudung";
                break;
            case 1:
                $class = "tt-sapdienra";
                break;
            case 2:
                $class = "tt-dadienra";
                break;
        }
        return $class;
    }
    protected function guard()
    {
        return Auth::guard(app('VoyagerGuard'));
    }
}
