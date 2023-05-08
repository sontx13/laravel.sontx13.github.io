<?php

namespace App\Http\Controllers\Voyager;

use App\Enums\BuoiHopEnum;
use App\Enums\TrangThaiChatVanEnum;
use App\Enums\TrangThaiHoatDongEnum;
use App\Enums\TrangThaiPhienChatVanEnum;
use App\Enums\VaiTroEnum;
use App\Helpers\FireBaseHelper;
use App\Models\ChatVan;
use App\Models\DaiBieuKyHop;
use App\Models\DiemDanh;
use App\Models\KhoaHop;
use App\Models\KyHop;
use App\Models\ThoigianKyhop;
use App\Models\TraloiChatvan;
use App\Models\User;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatVanController extends VoyagerBaseController
{
    // View Index Chất vấn
    public function Index(Request $request)
    {
        $this->authorize('browse_chat-van');
        $lsKhoahop = KhoaHop::where('trang_thai', TrangThaiHoatDongEnum::HoatDong)
            ->get();
        $view = 'voyager::chat-van.index';
        return Voyager::view($view,compact('lsKhoahop'));
    }

    // Partial view ds ngày họp
    public function PartialViewDsNgayHop(Request $request)
    {
        $kyhopId = $request->kyhopid;
        $lsNgayHops = ThoigianKyhop::where('kyhop_id', $kyhopId)
                                ->get();
        $view = 'voyager::chat-van.partials.ngay-hop';
        return Voyager::view($view,compact('lsNgayHops'));
    }

    // Partial view chất vấn
    public function PartialViewChatVan(Request $request)
    {
        $kyhopid = $request->kyhopid;
        $thoigianid = $request->thoigianid;
        $lsChatVans = TraloiChatvan::where('kyhop_id', $kyhopid)
                                    ->where('thoigian_kyhop_id', $thoigianid)
                                    //Sắp xếp theo trạng thái: Đang diễn ra ->
                                    ->orderByRaw("FIELD(trang_thai , '1', '0', '2') ASC")
                                    ->orderBy('stt')
                                    ->with('chatvans')
                                    ->get();
        $view = 'voyager::chat-van.partials.chat-van';
        return Voyager::view($view,compact('lsChatVans', 'kyhopid', 'thoigianid'));
    }

    ////////////////////////////////////////////////////////
    //REGION-----------Phiên Chất vấn----------------------
    public function ThemMoiPhienChatVan(Request $request) {
        $this->authorize('add_traloi-chatvan');
        $kyhopId = $request->kyhopid;
        $thoigianId = $request->thoigianid;
        $nguoitraloi = $request->nguoitraloi;
        $thoigian = $request->thoigian;
        $stt = $request->stt;
        $currentUserId = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::table('traloi_chatvan')->insert([
                'id' => 0,
                'phien_chatvan' => $nguoitraloi,
                'thoigian_batdau' => null,
                'thoigian_ketthuc' => null,
                'thoigian_phien' => $thoigian,
                'trang_thai' => TrangThaiPhienChatVanEnum::ChuaBatDau,
                'stt' => $stt,
                'kyhop_id' => $kyhopId,
                'thoigian_kyhop_id' => $thoigianId,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
                'deleted_at' => null,
                'created_by' => $currentUserId,
                'updated_by' => null,
                'deleted_by' => null
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

    public function CapNhatPhienChatVan(Request $request) {
        $this->authorize('edit_traloi-chatvan');
        $id = $request->id;
        $nguoitraloi = $request->nguoitraloi;
        $thoigian = $request->thoigian;
        $stt = $request->stt;
        $currentUserId = Auth::user()->id;
        $phienchatvan = TraloiChatvan::find($id);
        if ($phienchatvan == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Phiên chất vấn không tồn tại'
            ], 200);
        }
        else if ($phienchatvan->trang_thai == TrangThaiPhienChatVanEnum::DangDienRa
            || $phienchatvan->trang_thai == TrangThaiPhienChatVanEnum::DaHoanThanh) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Phiên chất vấn đang diễn ra hoặc đã hoàn thành không thể cập nhật'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('traloi_chatvan')
            ->where('id', $id)
            ->update([
                'phien_chatvan' => $nguoitraloi,
                'thoigian_phien' => $thoigian,
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

    public function XoaPhienChatVan(Request $request) {
        $this->authorize('delete_traloi-chatvan');
        $id = $request->id;
        $currentUserId = Auth::user()->id;
        $phienchatvan = TraloiChatvan::find($id);
        if ($phienchatvan == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Phiên chất vấn không tồn tại'
            ], 200);
        }
        else if ($phienchatvan->trang_thai == TrangThaiPhienChatVanEnum::DangDienRa) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Phiên chất vấn đang diễn ra không thể xóa'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('traloi_chatvan')
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

    public function BatDauPhienChatVan(Request $request) {
        $this->authorize('edit_traloi-chatvan');
        $id = $request->id;
        $kyhopid = $request->kyhopid;
        $currentUser = Auth::user();
        $phienchatvan = TraloiChatvan::find($id);
        if ($phienchatvan == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Phiên chất vấn không tồn tại'
            ], 200);
        }
        else if ($phienchatvan->trang_thai != TrangThaiPhienChatVanEnum::ChuaBatDau) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Phiên chất vấn đang diễn ra hoặc đã hoàn thành'
            ], 200);
        }
        // Kiểm tra có phiên chất vấn nào đang diễn ra hay không
        $dangDienRa = TraloiChatvan::where('kyhop_id', $kyhopid)
                                ->where('trang_thai', TrangThaiPhienChatVanEnum::DangDienRa)
                                ->get();
        if (count($dangDienRa) > 0) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Tồn tại '.count($dangDienRa). ' phiên chất vấn trong kỳ họp đang diễn ra'
            ], 200);
        }
        DB::beginTransaction();
        try {
            $tg_batdau = date("Y-m-d H:i:s");
            DB::table('traloi_chatvan')
            ->where('id', $id)
            ->update([
                'thoigian_batdau' =>  $tg_batdau,
                'trang_thai' => TrangThaiPhienChatVanEnum::DangDienRa,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUser->id
            ]);
            DB::commit();
            if (setting('site.usefirebase') == "1") {
                FireBaseHelper::SendEventBatDauPhienChatVan($currentUser->tenant_id,  $kyhopid, $id, $phienchatvan->phien_chatvan, $tg_batdau, $phienchatvan->thoigian_phien);
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

    public function KetThucPhienChatVat(Request $request) {
        $this->authorize('edit_traloi-chatvan');
        $id = $request->id;
        $currentUser = Auth::user();
        $phienchatvan = TraloiChatvan::find($id);
        if ($phienchatvan == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Phiên chất vấn không tồn tại'
            ], 200);
        }
        else if ($phienchatvan->trang_thai != TrangThaiPhienChatVanEnum::DangDienRa) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Phiên chất vấn đã hoàn thành hoặc chưa bắt đầu'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('traloi_chatvan')
            ->where('id', $id)
            ->update([
                'thoigian_ketthuc' => date("Y-m-d H:i:s"),
                'trang_thai' => TrangThaiPhienChatVanEnum::DaHoanThanh,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUser->id
            ]);
            DB::commit();
            if (setting('site.usefirebase') == "1") {
                Firebasehelper::SendEventKetThucPhienChatVan($currentUser->tenant_id, $phienchatvan->kyhop_id);
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
    ////////////////////////////////////////////////////////
    //END REGION-----------Phiên Chất vấn------------------


    ////////////////////////////////////////////////////////
    //REGION-----------Chất vấn----------------------------
    //Modal thêm mới, sửa đại biểu chất vấn
    public function PartialViewThemMoiDaiBieuChatVan(Request $request){
        $kyhopid = $request->kyhopid;
        $phienchatvanid = $request->phienchatvanid;
        $chatvanid = $request->id;
        // lấy ds đại biểu theo kỳ họp id
        $query =
            "SELECT
                daibieu.user_id as id,
                us.name as text
            FROM hdnd_app.daibieu_kyhop daibieu
            INNER JOIN hdnd_app.users us
                ON daibieu.user_id = us.id
            WHERE
                daibieu.kyhop_id = ?
            AND daibieu.vai_tro IN (0,1)";
        $lsDaiBieus = DB::select($query, [$kyhopid]);
        // lấy thông tin chất vấn theo chất vấn id
        $chatvan = null;
        if ($chatvanid > 0) {
            $chatvan = ChatVan::find($chatvanid);
            $title = "Sửa đại biểu chất vấn";
        }
        else {
            $title = "Thêm đại biểu chất vấn";
        }
        $view = 'voyager::chat-van.partials.modal-chatvan';
        return Voyager::view($view,compact('lsDaiBieus', 'phienchatvanid', 'chatvan', 'chatvanid', 'title'));
    }
    //Action thêm mới đại biểu chất vấn
    public function ThemMoiDaiBieuChatVan(Request $request) {
        $this->authorize('add_chat-van');
        $kyhopid = $request->kyhopid;
        $daibieuid = $request->daibieuid;
        $noidung = $request->noidung;
        $thoigian = $request->thoigian;
        $stt = $request->stt;
        $phienchatvanId = $request->phienchatvanId;
        $currentUserId = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::table('chat_van')->insert([
                'id' => 0,
                'nguoi_chatvan' => $daibieuid,
                'noidung_chatvan' => $noidung,
                'thoigian_dangky' => $thoigian,
                'thoigian_batdau' => null,
                'thoigian_ketthuc' => null,
                'trang_thai' => TrangThaiChatVanEnum::ChoDuyet,
                'stt' => $stt,
                'kyhop_id' => $kyhopid,
                'traloi_chatvan_id' => $phienchatvanId,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
                'deleted_at' => null,
                'created_by' => $currentUserId,
                'updated_by' => null,
                'deleted_by' => null
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
    //Action cập nhật đại biểu chất vấn
    public function CapNhatDaiBieuChatVan(Request $request) {
        $this->authorize('edit_chat-van');
        $id = $request->id;
        $daibieuid = $request->daibieuid;
        $noidung = $request->noidung;
        $thoigian = $request->thoigian;
        $stt = $request->stt;
        $currentUserId = Auth::user()->id;
        $chatvan = Chatvan::find($id);
        if ($chatvan == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai == TrangThaiChatVanEnum::DangChatVan
            || $chatvan->trang_thai == TrangThaiChatVanEnum::DaChatVan) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Đại biểu Chất vấn đang chất vấn hoặc đã chất vấn không thể cập nhật'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('chat_van')
            ->where('id', $id)
            ->update([
                'nguoi_chatvan' => $daibieuid,
                'noidung_chatvan' => $noidung,
                'thoigian_dangky' => $thoigian,
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
    //Action duyệt đại biểu chất vấn
    public function DuyetDaiBieuChatVan(Request $request) {
        $this->authorize('edit_chat-van');
        $id = $request->id;
        $currentUserId = Auth::user()->id;
        $chatvan = Chatvan::find($id);
        if ($chatvan == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai != TrangThaiChatVanEnum::ChoDuyet) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Trạng thái đại biểu chất vấn khác chờ duyệt'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('chat_van')
            ->where('id', $id)
            ->update([
                'trang_thai' => TrangThaiChatVanEnum::ChoChatVan,
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
    //Action xóa đại biểu chất vấn
    public function XoaDaiBieuChatVan(Request $request) {
        $this->authorize('delete_chat-van');
        $id = $request->id;
        $currentUser = Auth::user();
        $chatvan = Chatvan::find($id);
        if ($chatvan == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai == TrangThaiChatVanEnum::DangChatVan) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Đại biểu chất vấn đang chất vấn không thể xóa'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('chat_van')
            ->where('id', $id)
            ->update([
                'deleted_at' => date("Y-m-d H:i:s"),
                'deleted_by' => $currentUser->id
            ]);
            DB::commit();
            if (setting('site.usefirebase') == "1") {
                FirebaseHelper::SendEventXoaDaiBieuChatVan($currentUser->tenant_id, $chatvan->kyhop_id,
                $chatvan->traloi_chatvan_id, $chatvan->nguoi_chatvan);
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
    //Action đại biểu bắt đầu chất vấn
    public function BatDauDaiBieuChatVan(Request $request) {
        $this->authorize('edit_chat-van');
        $id = $request->id;
        $currentUser = Auth::user();
        $chatvan = ChatVan::find($id);
        $phienChatVan = $chatvan->traloichatvan;
        if ($chatvan == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai != TrangThaiChatVanEnum::ChoChatVan) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Trạng thái đại biểu chất vấn khác chờ chất vấn'
            ], 200);
        }
        if ($phienChatVan->trang_thai != TrangThaiPhienChatVanEnum::DangDienRa) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Phiên chất vấn không đang diễn ra'
            ], 200);
        }
        // Kiểm tra phiên chất vấn có đang chất vấn đại biểu nào không?
        $dangChatVan = ChatVan::where('traloi_chatvan_id', $phienChatVan->id)
                            ->where('trang_thai', TrangThaiChatVanEnum::DangChatVan)
                            ->get();
        if (count($dangChatVan) > 0) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Tồn tại '.count($dangChatVan).' đại biểu đang chất vấn'
            ], 200);
        }
        DB::beginTransaction();
        try {
            $tg_batdau = date("Y-m-d H:i:s");
            DB::table('chat_van')
            ->where('id', $id)
            ->update([
                'thoigian_batdau' => $tg_batdau,
                'trang_thai' => TrangThaiChatVanEnum::DangChatVan,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUser->id
            ]);
            DB::commit();
            if (setting('site.usefirebase') == "1") {
                FireBaseHelper::SendEventBatDauDaiBieuChatVan($currentUser->tenant_id, $chatvan->kyhop_id,
                    $phienChatVan->id , $chatvan->id, $chatvan->nguoi_chatvan, $chatvan->user->name, $tg_batdau, $chatvan->thoigian_dangky);
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
    //Action kết thúc đại biểu chất vấn
    public function KetThucDaiBieuChatVat(Request $request) {
        $this->authorize('edit_chat-van');
        $id = $request->id;
        $currentUser = Auth::user();
        $chatvan = ChatVan::find($id);
        $phienChatVan = $chatvan->traloichatvan;
        if ($chatvan == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai != TrangThaiChatVanEnum::DangChatVan) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Trạng thái đại biểu chất vấn khác đang chất vấn'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('chat_van')
            ->where('id', $id)
            ->update([
                'thoigian_ketthuc' => date("Y-m-d H:i:s"),
                'trang_thai' => TrangThaiChatVanEnum::DaChatVan,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUser->id
            ]);
            DB::commit();
            if (setting('site.usefirebase') == "1") {
                FireBaseHelper::SendEventKetThucDaiBieuChatVan($currentUser->tenant_id, $chatvan->kyhop_id,
                    $phienChatVan->id, $chatvan->nguoi_chatvan);
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
    //END REGION-----------Chất vấn----------------------------
    ///////////////////////////////////////////////////////////


    //Partial view trình chiếu chất vấn
    public function PartialViewTrinhChieu(Request $request) {
        $currentUser = Auth::user();
        $tenantId = $currentUser->tenant_id;
        $id = $request->id;
        $kyhopid = $request->kyhopid;
        $phienchatvanid = 0;
        $phienchatvan = null;
        $thoigianbatdau = "";
        $thoigianphien = "";
        $chatvan = "";
        $phienDangDienRa = false;
        $isTrinhChieu = false;
        if ($id > 0) {
            $phienchatvan = TraloiChatvan::where('id', $id)
                                    ->with('chatvans')
                                    ->first();
                            $thoigianbatdau = $phienchatvan->thoigian_batdau;
                            $thoigianphien = $phienchatvan->thoigian_phien;
                            $chatvan = json_encode($phienchatvan->chatvans);
        } else {
            $isTrinhChieu = true;
            //Phiên chất vấn đang diễn ra
            $phienchatvan = TraloiChatvan::where('kyhop_id', $kyhopid)
                                    ->where('trang_thai', TrangThaiPhienChatVanEnum::DangDienRa)
                                    ->with('chatvans')
                                    ->with('chatvans.user')
                                    ->first();
            if ($phienchatvan != null) {
                $thoigianbatdau = $phienchatvan->thoigian_batdau;
                $thoigianphien = $phienchatvan->thoigian_phien;
                $phienchatvanid = $phienchatvan->id;
                $chatvan = json_encode($phienchatvan->chatvans);
            }
        }
        $kyhop = KyHop::find($kyhopid);
        $view = 'voyager::chat-van.partials.trinh-chieu-chat-van';
        return Voyager::view($view,compact('phienchatvan', 'thoigianbatdau', 'thoigianphien',
         'chatvan', 'kyhop', 'phienchatvanid', 'isTrinhChieu', 'tenantId', 'kyhopid' ));
    }

    public static function GetDisplayTTChatVan($trangthai) {
        $result = "";
        switch($trangthai) {
            case "0":
                $result = "Chờ duyệt";
                break;
            case "1":
                $result = "Chờ chất vấn";
                break;
            case "2":
                $result = "Đang chất vấn";
                break;
            case "3":
                $result = "Đã chất vấn";
                break;
        }
        return $result;
    }

    public static function GetDisplayTTPhienChatVan($trangthai) {
        $result = "";
        switch($trangthai) {
            case "0":
                $result = "Chưa bắt đầu";
                break;
            case "1":
                $result = "Đang diễn ra";
                break;
            case "2":
                $result = "Đã hoàn thành";
                break;
        }
        return $result;
    }
    protected function guard()
    {
        return Auth::guard(app('VoyagerGuard'));
    }
}
