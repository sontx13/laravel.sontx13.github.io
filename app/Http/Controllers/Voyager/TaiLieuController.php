<?php

namespace App\Http\Controllers\Voyager;

use App\Enums\DangTaiLieuEnum;
use App\Enums\LoaiTaiLieuEnum;
use App\Enums\TrangThaiHoatDongEnum;
use App\Helpers\FileHelper;
use App\Models\KhoaHop;
use App\Models\LoaiTailieu;
use App\Models\TaiLieu;
use App\Models\ThoiGianKyHop;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaiLieuController extends VoyagerBaseController
{
    // View Index tài liệu
    public function Index(Request $request)
    {
        $loai = $request->loai;
        $url = url()->current();
        if (str_contains($url, 'tai-lieu')) {
            $loai = LoaiTaiLieuEnum::TaiLieu;
            $this->authorize('browse_tai-lieu');
        }
        else if (str_contains($url, 'duthao-nghiquyet')) {
            $loai = LoaiTaiLieuEnum::DuThaoNghiQuyet;
            $this->authorize('browse_duthao-nghiquyet');
        }
        else if (str_contains($url, 'nghi-quyet')) {
            $loai = LoaiTaiLieuEnum::NghiQuyetThongQua;
            $this->authorize('browse_nghi-quyet');
        }
        $lsKhoahop = KhoaHop::where('trang_thai', TrangThaiHoatDongEnum::HoatDong)
            ->get();
        $view = 'voyager::tai-lieu.index';
        return Voyager::view($view,compact('lsKhoahop', 'loai'));
    }

    public function PartialViewDsTaiLieu(Request $request) {
        $kyhopId = $request->kyhopid;
        $parentid = $request->parentid;
        $loai = $request->loai;
        $dsTaiLieu = TaiLieu::where('kyhop_id', $kyhopId)
            ->where('loai_tailieu', $loai)
            ->where('parent_id', $parentid)
            ->orderByRaw("FIELD(type , '0', '1') ASC") //Sắp xếp thư mục lên trước, file xuống sau
            ->orderBy('stt')
            ->get();
        $view = 'voyager::tai-lieu.partials.danh-sach';
        return Voyager::view($view,compact('dsTaiLieu'));
    }

    public function TaoThuMuc(Request $request) {
        $kyhopId = $request->kyhopid;
        $parentid = $request->parentid;
        $tenthumuc = $request->tenthumuc;
        $stt = $request->stt;
        $loai = $request->loai;
        $this->CheckPermission($loai, 'add_tai-lieu', 'add_duthao-nghiquyet', 'add_nghi-quyet');
        $currentUser = Auth::user();
        if ($tenthumuc == "" || $tenthumuc == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Tên thư mục không được để trống'
            ], 200);
        } else {
            $existsFolder = TaiLieu::where('kyhop_id', $kyhopId)
                                    ->where('parent_id', $parentid)
                                    ->where('ten_tailieu', $tenthumuc)
                                    ->first();
            if ($existsFolder != null) {
                $tenthumuc = $tenthumuc.'(new)';
            }
        }
        DB::beginTransaction();
        try {
            DB::table('tai_lieu')->insert([
                'id' => 0,
                'ten_tailieu' => $tenthumuc,
                'parent_id' => $parentid,
                'loai_tailieu' => $loai,
                'type' => DangTaiLieuEnum::ThuMuc,
                'stt' => $stt,
                'kyhop_id' => $kyhopId,
                'lichhop_id' => null,
                'thoigian_kyhop_id' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
                'deleted_at' => null,
                'created_by' => $currentUser->id,
                'updated_by' => null,
                'deleted_by' => null,
                'tenant_id' => $currentUser->tenant_id
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

    public function PartialViewFolder(Request $request) {
        $folderid = $request->folderid;
        $tailieu = TaiLieu::find($folderid);
        if ($tailieu == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Thư mục không tồn tại'
            ], 200);
        }
        $view = 'voyager::tai-lieu.partials.view-folder';
        return Voyager::view($view,compact('tailieu'));
    }

    public function CapNhatThuMuc(Request $request) {
        $folderid = $request->id;
        $tailieu = TaiLieu::find($folderid);
        $tenthumuc = $request->tenthumuc;
        $stt = $request->stt;
        $currentUser = Auth::user();
        if ($tailieu == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Thư mục không tồn tại'
            ], 200);
        } else {
            $this->CheckPermission($tailieu->loai_tailieu, 'edit_tai-lieu', 'edit_duthao-nghiquyet', 'edit_nghi-quyet');
            DB::beginTransaction();
            try {
                DB::table('tai_lieu')
                ->where('id', $folderid)
                ->update([
                    'ten_tailieu' => $tenthumuc,
                    'stt' => $stt,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => $currentUser->id
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
    }

    public function Upload(Request $request) {
        if (!isset($_FILES)) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Danh sách file trống'
            ], 200);
        }
        $kyhopid = $request->all()['kyhopid'];
        $folderid = $request->all()['folderid'];
        $loai = $request->all()['loai'];
        $this->CheckPermission($loai, 'add_tai-lieu', 'add_duthao-nghiquyet', 'add_nghi-quyet');
        $currentUser = Auth::user();
        $folder = TaiLieu::find($folderid);
        if ($folderid != 0 && $folder == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Thư mục không tồn tại'
            ], 200);
        }
        $success = 0;
        $files = $request->file();
        foreach ($files as $file) {
            DB::beginTransaction();
            try {
                // Upload ds file lên file server
                // return link absolute
                $originalFile = $file->getClientOriginalName();
                $filename = date('YmdHis').'_'.$originalFile;
                $url = FileHelper::Save($file, $currentUser->tenant_id, $kyhopid, $filename);
                if ($url != '') {
                    // Insert thông tin vào bảng tài liệu
                    $idTaiLieu = DB::table('tai_lieu')->insertGetId([
                        'id' => 0,
                        'ten_tailieu' => $filename,
                        'parent_id' => $folderid,
                        'loai_tailieu' => $loai,
                        'type' => DangTaiLieuEnum::File,
                        'stt' => null,
                        'kyhop_id' => $kyhopid,
                        'lichhop_id' => null,
                        'thoigian_kyhop_id' => null,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => null,
                        'deleted_at' => null,
                        'created_by' => $currentUser->id,
                        'updated_by' => null,
                        'deleted_by' => null,
                        'tenant_id' => $currentUser->tenant_id
                    ]);

                    DB::table('tailieu_chitiet')->insert([
                        'id' => 0,
                        'tailieu_id' => $idTaiLieu,
                        'so_kyhieu' => null,
                        'trich_yeu' => null,
                        'doituong_tailieu' => null,
                        'url' => $url,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => null,
                        'deleted_at' => null,
                        'created_by' => $currentUser->id,
                        'updated_by' => null,
                        'deleted_by' => null,
                        'tenant_id' => $currentUser->tenant_id,
                        'file_name' => $filename,
                        'file_extension' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'ngay_vanban' => null
                    ]);
                    $success++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error_code' => '1',
                    'ex' => json_encode($e)
                ], 200);
            }
        }
        return response()->json([
            'error_code' => $success == count($files) ? '0' : '1',
            'message' => 'Lưu '.$success.'/'.count($files).' file thành công'
        ], 200);
    }

    public function PartialViewFile(Request $request) {
        $fileid = $request->fileid;
        $tailieu = TaiLieu::find($fileid);
        if ($tailieu == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'File không tồn tại'
            ], 200);
        }
        $dmLoaiTaiLieu = LoaiTailieu::where('status', 1)
                                ->get();
        $ngayhops = ThoiGianKyHop::where('kyhop_id', $tailieu->kyhop_id)
                                ->orderBy('ngay_dienra')
                                ->get();
        $view = 'voyager::tai-lieu.partials.view-file';
        return Voyager::view($view,compact('tailieu', 'dmLoaiTaiLieu', 'ngayhops'));
    }

    public function CapNhatFile(Request $request) {
        $fileid = $request->id;
        $tenfile = $request->tenfile;
        $sokyhieu = $request->sokyhieu;
        $ngayvanban = $request->ngayvanban;
        $trichyeu = $request->trichyeu;
        $phanloai = $request->phanloai;
        $doituong = $request->doituong;
        $stt = $request->stt;
        $currentUser = Auth::user();
        $file = TaiLieu::find($fileid);
        if ($file == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'File không tồn tại'
            ], 200);
        } else {
            $this->CheckPermission($file->loai_tailieu, 'edit_tai-lieu', 'edit_duthao-nghiquyet', 'edit_nghi-quyet');
            DB::beginTransaction();
            try {
                DB::table('tai_lieu')
                ->where('id', $fileid)
                ->update([
                    'ten_tailieu' => $tenfile,
                    'stt' => $stt,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => $currentUser->id
                ]);

                DB::table('tailieu_chitiet')
                ->where('tailieu_id', $fileid)
                ->update([
                    'loai_tailieu' => $phanloai,
                    'so_kyhieu' => $sokyhieu,
                    'trich_yeu' => $trichyeu,
                    'doituong_tailieu' => $doituong,
                    'ngay_vanban' => $ngayvanban,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => $currentUser->id
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
    }

    public function XoaFileDinhKem(Request $request) {
        $fileid = $request->id;
        $currentUser = Auth::user();
        $file = TaiLieu::find($fileid);
        if ($file == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'File không tồn tại'
            ], 200);
        } else {
            $this->CheckPermission($file->loai_tailieu, 'edit_tai-lieu', 'edit_duthao-nghiquyet', 'edit_nghi-quyet');
            if (FileHelper::Remove($file->tailieuchitiet->url)) {
                DB::beginTransaction();
                try {
                    DB::table('tailieu_chitiet')
                    ->where('tailieu_id', $fileid)
                    ->update([
                        'url' => null,
                        'file_name' => null,
                        'file_extension' => null,
                        'file_size' => null,
                        'ngay_vanban' => null,
                        'updated_at' => date("Y-m-d H:i:s"),
                        'updated_by' => $currentUser->id
                    ]);
                    DB::commit();
                    return response()->json([
                        'error_code' => '0',
                        'message' => 'Xóa file đính kèm thành công'
                    ], 200);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'error_code' => '1',
                        'message' => 'Xóa file đính kèm không thành công',
                        'data' => json_encode($e)
                    ], 200);
                }
            } else {
                return response()->json([
                    'error_code' => '1',
                    'message' => 'Xóa file đính kèm không thành công'
                ], 200);
            }
        }
    }

    public function UploadFileDinhKem(Request $request) {
        if (!isset($_FILES)) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Danh sách file trống'
            ], 200);
        }
        $fileid = $request->all()['fileid'];
        $currentUser = Auth::user();
        $tailieu = TaiLieu::find($fileid);
        if ($fileid != 0 && $tailieu == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Tài liệu không tồn tại'
            ], 200);
        }
        $this->CheckPermission($tailieu->loai_tailieu, 'edit_tai-lieu', 'edit_duthao-nghiquyet', 'edit_nghi-quyet');
        $files = $request->file();
        foreach ($files as $file) {
            DB::beginTransaction();
            try {
                // Upload ds file lên file server
                // return link absolute
                $originalFile = $file->getClientOriginalName();
                $filename = date('YmdHis').'_'.$originalFile;
                $url = FileHelper::Save($file, $currentUser->tenant_id, $tailieu->kyhop_id, $filename);
                if ($url != '') {
                    DB::table('tailieu_chitiet')
                    ->where('tailieu_id', $fileid)
                    ->update([
                        'url' => $url,
                        'updated_at' => date("Y-m-d H:i:s"),
                        'updated_by' => $currentUser->id,
                        'file_name' => $filename,
                        'file_extension' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize()
                    ]);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error_code' => '1',
                    'ex' => json_encode($e)
                ], 200);
            }
        }
        return response()->json([
            'error_code' => '0',
            'message' => 'Lưu file thành công'
        ], 200);
    }

    public function XoaTaiLieu(Request $request) {
        $tailieuid = $request->id;
        $currentUser = Auth::user();
        $tailieu = TaiLieu::find($tailieuid);
        if ($tailieu == null) {
            return response()->json([
                'error_code' => '1',
                'message' => 'Tài liệu không tồn tại'
            ], 200);
        }
        $this->CheckPermission($tailieu->loai_tailieu, 'delete_tai-lieu', 'delete_duthao-nghiquyet', 'delete_nghi-quyet');
        if ($tailieu->type == DangTaiLieuEnum::File) {
            FileHelper::Remove($tailieu->tailieuchitiet->url);
            DB::beginTransaction();
            try {
                DB::table('tai_lieu')
                ->where('id', $tailieuid)
                ->update([
                    'deleted_at' => date("Y-m-d H:i:s"),
                    'deleted_by' => $currentUser->id
                ]);

                DB::table('tailieu_chitiet')
                ->where('tailieu_id', $tailieuid)
                ->update([
                    'deleted_at' => date("Y-m-d H:i:s"),
                    'deleted_by' => $currentUser->id
                ]);
                DB::commit();
                return response()->json([
                    'error_code' => '0',
                    'message' => 'Xóa tài liệu thành công'
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'error_code' => '1',
                    'message' => 'Xóa tài liệu không thành công',
                    'data' => json_encode($e)
                ], 200);
            }
        } else {
            $this->DeleteTaiLieu($tailieuid);
            return response()->json([
                'error_code' => '0',
                'message' => 'Xóa thư mục thành công'
            ], 200);
        }
    }

    private function DeleteTaiLieu($tailieuid) {
        $currentUser = Auth::user();
        $lsTaiLieuChilds = TaiLieu::where('parent_id', $tailieuid)
                                    ->get();
        if (count($lsTaiLieuChilds) > 0) {
            // Duyệt ds tài liệu con
            foreach ($lsTaiLieuChilds as $childTaiLieu) {
                if ($childTaiLieu->type == DangTaiLieuEnum::File) {
                    // Nếu node tài liệu con là file => Xóa file
                    $this->DeleteFile($childTaiLieu, $currentUser->id);
                } else {
                    // Nếu node tài liệu con là thư mục => Duyệt lại hàm đệ quy
                    $this->DeleteTaiLieu($childTaiLieu->id);
                }
            }
            // Thực hiện xóa folder tài liệu
            $this->DeleteFolder($tailieuid, $currentUser->id);
        } else {
            // Thực hiện xóa folder tài liệu
            $this->DeleteFolder($tailieuid, $currentUser->id);
        }
    }

    private function DeleteFolder($folderid, $userid) {
        DB::beginTransaction();
        try {
            DB::table('tai_lieu')
            ->where('id', $folderid)
            ->update([
                'deleted_at' => date("Y-m-d H:i:s"),
                'deleted_by' => $userid
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    private function DeleteFile($file, $userid) {
        FileHelper::Remove($file->tailieuchitiet->url);
        DB::beginTransaction();
        try {
            DB::table('tai_lieu')
            ->where('id', $file->id)
            ->update([
                'deleted_at' => date("Y-m-d H:i:s"),
                'deleted_by' => $userid
            ]);

            DB::table('tailieu_chitiet')
            ->where('tailieu_id', $file->id)
            ->update([
                'deleted_at' => date("Y-m-d H:i:s"),
                'deleted_by' => $userid
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
    private function CheckPermission($loai, $permissionNameTaiLieu, $permissionNameDTNQ, $permissionNameNQ) {
        switch ($loai) {
            case LoaiTaiLieuEnum::TaiLieu:
                $this->authorize($permissionNameTaiLieu);
                break;
            case LoaiTaiLieuEnum::DuThaoNghiQuyet:
                $this->authorize($permissionNameDTNQ);
                break;
            case LoaiTaiLieuEnum::NghiQuyetThongQua:
                $this->authorize($permissionNameNQ);
                break;
        }
    }
    protected function guard()
    {
        return Auth::guard(app('VoyagerGuard'));
    }
}
