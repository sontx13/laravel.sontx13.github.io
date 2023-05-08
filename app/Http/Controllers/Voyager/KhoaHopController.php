<?php

namespace App\Http\Controllers\Voyager;

use App\Enums\TrangThaiHoatDongEnum;
use App\Models\KhoaHop;
use App\Models\KyHop;
use App\Models\User;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Enums\TrangThaiKhoaHopEnum;
use App\Models\DaibieuKhoahop;
use App\Models\DonVi;
use Illuminate\Support\Facades\Gate;

class KhoaHopController extends VoyagerBaseController
{

    // View Ds khóa họp
    public function Index(Request $request)
    {
        $view = 'voyager::khoa-hop.index';
        $this->authorize('browse_khoa-hop');
        return Voyager::view($view);
    }

    //Action GetList kỳ họp theo ID khóa họp
    public function Getlistkhoahop(Request $request) {
        $this->authorize('browse_khoa-hop');
        $trangthai=$request->trangthai;
        $tenkhoahop=$request->tenkhoahop;
        $currentTenantId = Auth::user()->tenant_id;

        if($tenkhoahop == '' || $tenkhoahop == null){
            $up_tenkhoahop='';
        }
        else{
            $up_tenkhoahop=strtoupper($tenkhoahop);
        }

        $query="SELECT *
            FROM khoa_hop
            WHERE
                (? = -1 OR trang_thai = ?)
               AND tenant_id=?
               AND deleted_by IS NULL
               AND
                UPPER(ten_khoa_hop) like UPPER(CONCAT('%', ?, '%'))";

        $listKhoaHop=DB::select($query, [$trangthai, $trangthai,$currentTenantId, $up_tenkhoahop]);

        $view = 'voyager::khoa-hop.partials.dskhoahop';
        return Voyager::view($view,compact('listKhoaHop'));
    }

    // Thêm mới khóa họp
    public function ThemMoiKhoaHop(Request $request) {
        $this->authorize('add_khoa-hop');
        $tenKhoaHop = $request->tenkhoahop;

        $tgBatDau =date('Y-m-d',strtotime($request->tgbatdau));
        $tgKetThuc = date('Y-m-d',strtotime($request->tgketthuc));

        $currentUserId = Auth::user()->id;
        $currentTenantId = Auth::user()->tenant_id;
        DB::beginTransaction();
        try {
            DB::table('khoa_hop')->insert([
                'id' => 0,
                'ten_khoa_hop' => $tenKhoaHop,
                'ngay_bat_dau' => $tgBatDau,
                'ngay_ket_thuc' => $tgKetThuc,
                'trang_thai' => TrangThaiKhoaHopEnum::HoatDong,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
                'deleted_at' => null,
                'created_by' => $currentUserId,
                'updated_by' => null,
                'deleted_by' => null,
                'tenant_id' => $currentTenantId,
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
     // Sửa khóa họp
     public function CapNhatKhoaHop(Request $request) {
        $this->authorize('edit_khoa-hop');
        $tenKhoaHop = $request->tenkhoahop;
        $iD =$request->id;
        $tgBatDau =date('Y-m-d',strtotime($request->tgbatdau));
        $tgKetThuc = date('Y-m-d',strtotime($request->tgketthuc));

        $currentUserId = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::table('khoa_hop')
            ->where('id', $iD)
            ->update([
                'ten_khoa_hop' => $tenKhoaHop,
                'ngay_bat_dau' => $tgBatDau,
                'ngay_ket_thuc' => $tgKetThuc,
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
    // Xóa khóa họp
    public function XoaKhoaHop(Request $request) {
        $this->authorize('delete_khoa-hop');
        $id = $request->id;
        $currentUserId = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::table('khoa_hop')
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
    // Chuyển trạng thái
    public function ChuyenTrangThai(Request $request) {
        $this->authorize('edit_khoa-hop');
        $id = $request->id;

        if($request->trangthai==1){
            $trangthai = TrangThaiKhoaHopEnum::KhongHoatDong();
        }
        else{
            $trangthai = TrangThaiKhoaHopEnum::HoatDong();
        }
        $currentUserId = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::table('khoa_hop')
            ->where('id', $id)
            ->update([
                'trang_thai' => $trangthai,
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

    public function PartialViewModalDsDaiBieu(Request $request) {
        $this->authorize('browse_daibieu-khoahop');
        $khoahopid = $request->khoahopid;
        $currentUser = Auth::user();
        $lsUsers = User::where('tenant_id', $currentUser->id);
        $lsDonvis = DonVi::where('status', 1)
                        ->get();
        $view = 'voyager::khoa-hop.partials.modal-danh-sach-daibieu-khoahop';
        return Voyager::view($view,compact('khoahopid', 'lsUsers', 'lsDonvis'));
    }

    public function PartialViewDsDaiBieu(Request $request) {
        $khoahopid = $request->khoahopid;
        $currentUser = Auth::user();
        $dsDaiBieus = DaibieuKhoahop::where('khoahop_id', $khoahopid)
                                ->orderBy('stt')
                                ->get();
        $view = 'voyager::khoa-hop.partials.danh-sach-daibieu-khoahop';
        return Voyager::view($view,compact('khoahopid', 'dsDaiBieus'));
    }

    // Lấy danh sách người dùng theo đơn vị
    public function GetNguoiDungs(Request $request) {
        $khoahopid = $request->khoahopid;
        $donviid = $request->donviid;
        $currentUser = Auth::user();
        $lsUsers = User::leftJoin('daibieu_khoahop', function($join) use($khoahopid) {
                            $join->on('daibieu_khoahop.user_id', '=', 'users.id');
                            $join->on('daibieu_khoahop.khoahop_id', '=', DB::raw("'".$khoahopid."'"));
                        })
                        ->whereNull('daibieu_khoahop.id')
                        ->where(function ($q) use($donviid) {
                                if ($donviid != 0) {
                                    $q->where('users.donvi_id', $donviid);
                                }
                        })
                        ->where('users.tenant_id', $currentUser->tenant_id)
                        ->where('users.is_active', TrangThaiHoatDongEnum::HoatDong)
                        ->where('users.is_admin', '!=' , 1)
                        ->orderBy('users.name')
                        ->select(['users.id', 'users.name as text'])
                        ->get();
        return response()->json([
            'error_code' => '0',
            'message' => '',
            'data' => $lsUsers
        ], 200);
    }

    // Thêm đại biểu khóa họp
    public function AddDaiBieuKhoaHop(Request $request) {
        $this->authorize('add_daibieu-khoahop');
        $khoahopid = $request->khoahopid;
        $donviid = $request->donviid;
        $nguoidungid = $request->nguoidungid;
        $currentUser = Auth::user();
        DB::beginTransaction();
        try {
            $query = "
            INSERT INTO hdnd_app.daibieu_khoahop
            SELECT
                0 AS id,
                u.id as user_id,
                ? as khoahop_id,
                null as stt
            FROM hdnd_app.users u
            LEFT JOIN hdnd_app.daibieu_khoahop daibieu
                ON u.id = daibieu.user_id
                AND daibieu.khoahop_id = ?
            WHERE
                daibieu.id IS NULL
                AND u.is_active = 1
                AND u.tenant_id = ?
                AND (? = 0 OR u.donvi_id = ?)
                AND (? = 0 OR u.id = ?)";
            DB::insert($query, [$khoahopid, $khoahopid, $currentUser->tenant_id, $donviid, $donviid, $nguoidungid, $nguoidungid]);
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
    public function XoaDaiBieuKhoaHop(Request $request) {
        $this->authorize('delete_daibieu-khoahop');
        $khoahopid = $request->khoahopid;
        $daibieuid = $request->daibieuid;
        DB::beginTransaction();
        try {
            DB::table('daibieu_khoahop')
            ->where('user_id', $daibieuid)
            ->where('khoahop_id', $khoahopid)
            ->delete();
            DB::commit();
            return response()->json([
                'error_code' => '0',
                'message' => 'Xóa thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => '1',
                'message' => 'Xóa không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }

    public function SapXepBieuKhoaHop(Request $request) {
        $this->authorize('edit_daibieu-khoahop');
        $khoahopid = $request->khoahopid;
        $lsOrders = $request->lsOrders;
        DB::beginTransaction();
        try {
            foreach($lsOrders as $itemOrder) {
                DB::table('daibieu_khoahop')
                ->where('khoahop_id', $khoahopid)
                ->where('user_id', $itemOrder["id"])
                ->update([
                        "stt" =>  $itemOrder["stt"]
                    ]);
            }
            DB::commit();
            return response()->json([
                'error_code' => 0,
                'message' => 'Cập nhật số thứ tự thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => 1,
                'message' => 'Cập nhật số thứ tự không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }
    public static function GetClassTrangThaiKhoaHop($trangthai) {
        $class = "";
        if ($trangthai == 1) {
            $class = "tt-hoatdong";
        } else {
            $class = "tt-khonghoatdong";
        }
        return $class;
    }
    public static function GetDisplayTTKhoaHop($trangthai) {
        $result = "";
        if ($trangthai == 1) {
            $result = "Hoạt động";
        }
        else {
            $result = "Không hoạt động";
        }
        return $result;
    }
    protected function guard()
    {
        return Auth::guard(app('VoyagerGuard'));
    }
}
