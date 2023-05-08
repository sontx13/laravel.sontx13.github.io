<?php

namespace App\Http\Controllers;

use App\Enums\BuoiHopEnum;
use App\Enums\TrangThaiChatVanEnum;
use App\Enums\TrangThaiDiemDanhEnum;
use App\Enums\TrangThaiKyHopEnum;
use App\Enums\TrangThaiPhienChatVanEnum;
use App\Enums\VaiTroEnum;
use App\Helpers\FireBaseHelper;
use App\Helpers\LocationHelper;
use App\Models\ChatVan;
use App\Models\DaiBieuKyHop;
use App\Models\LichHop;
use App\Models\DiemDanh;
use App\Models\KyHop;
use App\Models\TraloiChatvan;
use http\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class ChatVanApiController extends Controller
{

    public $successStatus = Response::HTTP_OK;

    public function GetDsNguoiTraLoi(Request $request)
    {
        try {
            $kyhop = KyHop::where('trang_thai', TrangThaiKyHopEnum::DangDienRa)
                            ->first();
            $danhsach = [];
            if ($kyhop != null) {
                $danhsach = TraloiChatvan::where('kyhop_id', $kyhop->id)
                            ->orderBy('stt')
                            ->get();
            }
            return response()->json([
                'error_code' => 0,
                'data' => $danhsach,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 1,
                'message' => json_encode($e),
            ], Response::HTTP_OK);
        }
    }

    public function GetDsChatVan(Request $request) {
        try {
            $kyhop = KyHop::where('trang_thai', TrangThaiKyHopEnum::DangDienRa)
                            ->first();
            $danhsach = [];
            if ($kyhop != null) {
                $currentUser = Auth::user();
                $vaitroKyhop = DaiBieuKyHop::where('user_id', $currentUser->id)
                                ->where('kyhop_id', $kyhop->id)
                                ->first();
                $configHost = env('APP_URL')."/storage/";
                $query =
                "SELECT
                    cv.id,
                    cv.nguoi_chatvan,
                    cv.noidung_chatvan,
                    cv.trang_thai,
                    cv.stt,
                    cv.traloi_chatvan_id,
                    trlcv.phien_chatvan,
                    trlcv.trang_thai as trangthai_phien,
                    trlcv.stt as stt_phien,
                    u.name,
                    cv.created_at,
                    CASE
                        WHEN u.avatar IS NOT NULL THEN CONCAT(?, u.avatar)
                        ELSE u.avatar
                    END AS avatar
                FROM hdnd_app.chat_van cv
                LEFT JOIN hdnd_app.traloi_chatvan trlcv
                    ON cv.traloi_chatvan_id = trlcv.id
                INNER JOIN hdnd_app.users u
                    ON cv.nguoi_chatvan = u.id
                WHERE
                    cv.kyhop_id = ?
                    AND (? = 1 OR cv.nguoi_chatvan = ?)
                    AND cv.deleted_at IS NULL
                    ORDER BY
                        FIELD(trlcv.trang_thai, '1' , '0', '2'),
                        FIELD(cv.trang_thai, '2', '1', '0', '3'),
                        trlcv.stt,
                        trlcv.id,
                        cv.stt";
                $danhsach = DB::select($query, [$configHost, $kyhop->id, $vaitroKyhop->vai_tro, $currentUser->id]);
            }
            return response()->json([
                'error_code' => 0,
                'data' => $danhsach,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 1,
                'message' => json_encode($e),
            ], Response::HTTP_OK);
        }
    }

    ////////////////////////////////////////////////////////
    //REGION-----------Phiên Chất vấn----------------------
    public function ThemMoiPhienChatVan(Request $request) {
        $kyhop = KyHop::where('trang_thai', TrangThaiKyHopEnum::DangDienRa)
                ->first();
        if ($kyhop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Không tồn tại kỳ họp nào đang diễn ra'
            ], 200);
        }
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
                'kyhop_id' => $kyhop->id,
                'thoigian_kyhop_id' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
                'deleted_at' => null,
                'created_by' => $currentUserId,
                'updated_by' => null,
                'deleted_by' => null
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

    public function CapNhatPhienChatVan(Request $request) {
        $id = $request->id;
        $nguoitraloi = $request->nguoitraloi;
        $thoigian = $request->thoigian;
        $stt = $request->stt;
        $currentUserId = Auth::user()->id;
        $phienchatvan = TraloiChatvan::find($id);
        if ($phienchatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Phiên chất vấn không tồn tại'
            ], 200);
        }
        else if ($phienchatvan->trang_thai == TrangThaiPhienChatVanEnum::DangDienRa
            || $phienchatvan->trang_thai == TrangThaiPhienChatVanEnum::DaHoanThanh) {
            return response()->json([
                'error_code' => 1,
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

    public function XoaPhienChatVan(Request $request) {
        $id = $request->id;
        $currentUserId = Auth::user()->id;
        $phienchatvan = TraloiChatvan::find($id);
        if ($phienchatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Phiên chất vấn không tồn tại'
            ], 200);
        }
        else if ($phienchatvan->trang_thai == TrangThaiPhienChatVanEnum::DangDienRa) {
            return response()->json([
                'error_code' => 1,
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

    public function BatDauPhienChatVan(Request $request) {
        $id = $request->id;
        $kyhop = KyHop::where('trang_thai', TrangThaiKyHopEnum::DangDienRa)
            ->first();
        if ($kyhop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Không tồn tại kỳ họp nào đang diễn ra'
            ], 200);
        }
        $currentUser = Auth::user();
        $phienchatvan = TraloiChatvan::find($id);
        if ($phienchatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Phiên chất vấn không tồn tại'
            ], 200);
        }
        else if ($phienchatvan->trang_thai != TrangThaiPhienChatVanEnum::ChuaBatDau) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Phiên chất vấn đang diễn ra hoặc đã hoàn thành'
            ], 200);
        }
        // Kiểm tra có phiên chất vấn nào đang diễn ra hay không
        $dangDienRa = TraloiChatvan::where('kyhop_id', $kyhop->id)
                                ->where('trang_thai', TrangThaiPhienChatVanEnum::DangDienRa)
                                ->get();
        if (count($dangDienRa) > 0) {
            return response()->json([
                'error_code' => 1,
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
                FireBaseHelper::SendEventBatDauPhienChatVan($currentUser->tenant_id, $kyhop->id, $id, $phienchatvan->phien_chatvan, $tg_batdau, $phienchatvan->thoigian_phien);
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
        $id = $request->id;
        $currentUser = Auth::user();
        $phienchatvan = TraloiChatvan::find($id);
        if ($phienchatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Phiên chất vấn không tồn tại'
            ], 200);
        }
        else if ($phienchatvan->trang_thai != TrangThaiPhienChatVanEnum::DangDienRa) {
            return response()->json([
                'error_code' => 1,
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
    ////////////////////////////////////////////////////////
    //END REGION-----------Phiên Chất vấn------------------


    ////////////////////////////////////////////////////////
    //REGION-----------Chất vấn----------------------------
    //Action thêm mới đại biểu chất vấn
    public function ThemMoiDaiBieuChatVan(Request $request) {
        $kyhop = KyHop::where('trang_thai', TrangThaiKyHopEnum::DangDienRa)
         ->first();
        if ($kyhop == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Không tồn tại kỳ họp nào đang diễn ra'
            ], 200);
        }
        $noidung = $request->noidung;
        $thoigian = $request->thoigian;
        $stt = $request->stt;
        $phienchatvanId = $request->phienchatvanid;
        $currentUserId = Auth::user()->id;
        $phienchatvan = TraloiChatvan::find($phienchatvanId);
        if ($phienchatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Phiên chất vấn không tồn tại'
            ], 200);
        }
        $vaitroKyhop = DaiBieuKyHop::where('user_id', $currentUserId)
                        ->where('kyhop_id', $kyhop->id)
                        ->first();
        if ($vaitroKyhop == null || $vaitroKyhop->vai_tro == VaiTroEnum::KhachMoi) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Người dùng không có quyền đăng ký chất vấn'
            ], 200);
        }
        $chatvan = ChatVan::where('nguoi_chatvan', $currentUserId)
                            ->where('traloi_chatvan_id', $phienchatvanId)
                            ->first();
        if ($chatvan != null
            && ($chatvan->trang_thai == TrangThaiChatVanEnum::ChoDuyet || $chatvan->trang_thai == TrangThaiChatVanEnum::ChoChatVan)) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đại biểu đã đăng ký chất vấn rồi'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('chat_van')->insert([
                'id' => 0,
                'nguoi_chatvan' => $currentUserId,
                'noidung_chatvan' => $noidung,
                'thoigian_dangky' => $thoigian,
                'thoigian_batdau' => null,
                'thoigian_ketthuc' => null,
                'trang_thai' => TrangThaiChatVanEnum::ChoDuyet,
                'stt' => $stt,
                'kyhop_id' => $kyhop->id,
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
    //Action cập nhật đại biểu chất vấn
    public function CapNhatDaiBieuChatVan(Request $request) {
        $id = $request->id;
        $noidung = $request->noidung;
        $thoigian = $request->thoigian;
        $currentUserId = Auth::user()->id;
        $chatvan = Chatvan::find($id);
        if ($chatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai != TrangThaiChatVanEnum::ChoDuyet) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Trạng thái khác chờ duyệt không thể sửa'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('chat_van')
            ->where('id', $id)
            ->update([
                'noidung_chatvan' => $noidung,
                'thoigian_dangky' => $thoigian,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUserId
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
    //Action duyệt đại biểu chất vấn
    public function DuyetDaiBieuChatVan(Request $request) {
        $id = $request->id;
        $thoigian = $request->thoigian;
        $currentUserId = Auth::user()->id;
        $chatvan = Chatvan::find($id);
        if ($chatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai != TrangThaiChatVanEnum::ChoDuyet) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Trạng thái đại biểu chất vấn khác chờ duyệt'
            ], 200);
        }
        $hasRoleChuToa = DaiBieuKyHop::where('user_id', $currentUserId)
                                ->where('vai_tro', VaiTroEnum::ChuToa)
                                ->where('kyhop_id', $chatvan->kyhop_id)
                                ->first();
        if ($hasRoleChuToa == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Người dùng không phải là chủ tọa'
            ], 200);
        }
        DB::beginTransaction();
        try {
            DB::table('chat_van')
            ->where('id', $id)
            ->update([
                'trang_thai' => TrangThaiChatVanEnum::ChoChatVan,
                'thoigian_dangky' => $thoigian,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $currentUserId
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
    //Action xóa đại biểu chất vấn
    public function XoaDaiBieuChatVan(Request $request) {
        $id = $request->id;
        $currentUser = Auth::user();
        $chatvan = Chatvan::find($id);
        if ($chatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai != TrangThaiChatVanEnum::ChoDuyet) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Chỉ có trạng thái chờ duyệt mới được xóa'
            ], 200);
        }
        if ($chatvan->nguoi_chatvan != $currentUser->id) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đại biểu không thể xóa chất vấn của người khác'
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
                'error_code' => 0,
                'message' => 'Xóa thành công'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error_code' => 1,
                'message' => 'Xóa không thành công',
                'data' => json_encode($e)
            ], 200);
        }
    }
    //Action đại biểu bắt đầu chất vấn
    public function BatDauDaiBieuChatVan(Request $request) {
        $id = $request->id;
        $currentUser = Auth::user();
        $chatvan = ChatVan::find($id);
        $phienChatVan = $chatvan->traloichatvan;
        if ($chatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai != TrangThaiChatVanEnum::ChoChatVan) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Trạng thái khác chờ chất vấn không thể bắt đầu'
            ], 200);
        }
        if ($phienChatVan->trang_thai != TrangThaiPhienChatVanEnum::DangDienRa) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Trạng thái phiên chất vấn khác đang diễn ra'
            ], 200);
        }
        // Kiểm tra phiên chất vấn có đang chất vấn đại biểu nào không?
        $dangChatVan = ChatVan::where('traloi_chatvan_id', $phienChatVan->id)
                            ->where('trang_thai', TrangThaiChatVanEnum::DangChatVan)
                            ->get();
        if (count($dangChatVan) > 0) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Tồn tại '.count($dangChatVan).' đại biểu đang chất vấn'
            ], 200);
        }
        $hasRoleChuToa = DaiBieuKyHop::where('user_id', $currentUser->id)
                                ->where('vai_tro', VaiTroEnum::ChuToa)
                                ->where('kyhop_id', $chatvan->kyhop_id)
                                ->first();
        if ($hasRoleChuToa == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Người dùng không phải là chủ tọa không thể bắt đầu'
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
    //Action kết thúc đại biểu chất vấn
    public function KetThucDaiBieuChatVat(Request $request) {
        $id = $request->id;
        $currentUser = Auth::user();
        $chatvan = ChatVan::find($id);
        $phienChatVan = $chatvan->traloichatvan;
        if ($chatvan == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đại biểu chất vấn không tồn tại'
            ], 200);
        }
        else if ($chatvan->trang_thai != TrangThaiChatVanEnum::DangChatVan) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Trạng thái khác đang chất vấn không thể kết thúc'
            ], 200);
        }
        $hasRoleChuToa = DaiBieuKyHop::where('user_id', $currentUser->id)
                                ->where('vai_tro', VaiTroEnum::ChuToa)
                                ->where('kyhop_id', $chatvan->kyhop_id)
                                ->first();
        if ($hasRoleChuToa == null) {
            return response()->json([
            'error_code' => 1,
            'message' => 'Người dùng không phải là chủ tọa không thể kết thúc'
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
    //END REGION-----------Chất vấn----------------------------
    ///////////////////////////////////////////////////////////
}
