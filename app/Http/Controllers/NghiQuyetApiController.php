<?php

namespace App\Http\Controllers;

use App\Enums\LoaiTaiLieuEnum;
use http\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class NghiQuyetApiController extends Controller
{

    public $successStatus = Response::HTTP_OK;

    public function GetDsNghiQuyet(Request $request)
    {
        try {
            $kyhopid = $request->kyhopid;
            $folderid = $request->folderid;
            $tenantid = Auth::user()->tenant_id;
            $tailieu = $this->getTaiLieuKyhops($tenantid, $kyhopid, $folderid, LoaiTaiLieuEnum::NghiQuyetThongQua);
            return response()->json([
                'error_code' => 0,
                'data' =>  $tailieu,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 1,
                'message' => json_encode($e),
            ], Response::HTTP_OK);
        }
    }

    public function SearchNghiQuyet(Request $request)
    {
        try {
            $term = $request->term;
            $tenantid = Auth::user()->tenant_id;
            $kyhopid = $request->kyhopid;
            $tailieu = $this->fulltextSearchTaiLieu($tenantid, LoaiTaiLieuEnum::NghiQuyetThongQua, $kyhopid, $term);
            return response()->json([
                'error_code' => 0,
                'data' =>  $tailieu,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error_code' => 1,
                'message' => json_encode($e),
            ], Response::HTTP_OK);
        }
    }

    // Lấy danh sách tài liệu theo kỳ họp id, thư mục id,
    // loại: tài liệu, dự thảo nghị quyết, nghị quyết thông qua
    private function getTaiLieuKyhops($tenantid, $kyhopid, $folderid, $type) {
        $configHost = env('APP_URL');
        $query =
            "SELECT
                tl.id,
                tl.ten_tailieu,
                tl.parent_id,
                tl.type,
                tl.stt,
                ct.so_kyhieu,
                ct.trich_yeu,
                CONCAT(?, ct.url) AS url,
                ct.file_name,
                ct.file_extension,
                ct.file_size,
                ct.ngay_vanban
            FROM hdnd_app.tai_lieu tl
            INNER JOIN hdnd_app.tailieu_chitiet ct
                ON tl.id = ct.tailieu_id
            WHERE
                tl.deleted_at IS NULL
                AND tl.tenant_id = ?
                AND (? = 0 OR tl.kyhop_id = ?)
                AND tl.loai_tailieu = ?
                AND (? = 0 OR tl.parent_id = ?)
            ORDER BY FIELD(tl.type, 0, 1), tl.stt";
        $tailieus = DB::select($query, [$configHost, $tenantid, $kyhopid, $kyhopid, $type, $folderid, $folderid]);
        return $tailieus;
    }

    private function fulltextSearchTaiLieu($tenantid, $type, $kyhopid, $term) {
        $configHost = env('APP_URL');
        $query =
            "SELECT
                tl.id,
                tl.ten_tailieu,
                tl.parent_id,
                tl.type,
                tl.stt,
                ct.so_kyhieu,
                ct.trich_yeu,
                CONCAT(?, ct.url) AS url,
                ct.file_name,
                ct.file_extension,
                ct.file_size,
                ct.ngay_vanban
            FROM hdnd_app.tai_lieu tl
            INNER JOIN hdnd_app.tailieu_chitiet ct
                ON tl.id = ct.tailieu_id
            WHERE
                tl.tenant_id = ? AND
                tl.loai_tailieu = ? AND
                (? = 0 OR tl.kyhop_id = ?) AND
                tl.deleted_at IS NULL AND
                (
                    MATCH (tl.ten_tailieu)
                    AGAINST (? IN NATURAL LANGUAGE MODE)
                OR
                    MATCH (ct.so_kyhieu, ct.trich_yeu)
                    AGAINST (? IN NATURAL LANGUAGE MODE)
                )
            ORDER BY FIELD(tl.type, 0, 1), tl.stt";
        $tailieus = DB::select($query, [$configHost, $tenantid, $type, $kyhopid, $kyhopid , $term, $term]);
        return $tailieus;
    }
}
