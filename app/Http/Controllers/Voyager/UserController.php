<?php

namespace App\Http\Controllers\Voyager;

use App\Enums\TrangThaiHoatDongEnum;
use App\Helpers\FileHelper;
use App\Models\ChucVu;
use App\Models\DanToc;
use App\Models\DonVi;
use App\Models\User;
use DateTime;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Models\Role;

class UserController extends VoyagerBaseController
{
    // View Ds user
    public function Index(Request $request)
    {
        $currentUser = Auth::user();
        $donvis = DonVi::where('status', TrangThaiHoatDongEnum::HoatDong)->get();
        $dantocs = DanToc::where('status', TrangThaiHoatDongEnum::HoatDong)->get();
        $roles = Role::where('tenant_id', $currentUser->tenant_id)->get();
        $chucvus = ChucVu::where('status', TrangThaiHoatDongEnum::HoatDong)->get();
        $view = 'voyager::users.index';
        return Voyager::view($view,compact('donvis', 'dantocs', 'roles', 'chucvus'));
    }

    // Partial view danh sách user
    public function PartialViewDsUser(Request $request)
    {
        $currentUser = Auth::user();
        $donviid = $request->donviid;
        $lsUsers = User::where(function ($q) use($donviid) {
                        if ($donviid != 0) {
                            $q->where('donvi_id', $donviid);
                        }
                    })
                    ->where(function ($q) use($currentUser) {
                        // if ($currentUser->is_admin != 1) {
                            $q->where('is_admin', '!=', 1);
                        // }
                    })
                    ->where('tenant_id', $currentUser->tenant_id)
                    ->get();
        $view = 'voyager::users.partials.danh-sach';
        return Voyager::view($view,compact('lsUsers'));
    }
    public function CreateUser(Request $request) {
        $hoten = $request->hoten;
        $tendangnhap = $request->tendangnhap;
        $ngaysinh = $request->ngaysinh;
        $ngaysinh = $ngaysinh != null ? DateTime::createFromFormat('Y-m-d', $ngaysinh) : $ngaysinh;
        $gioitinh = $request->gioitinh;
        $dantoc = $request->dantoc;
        $tongiao = $request->tongiao;
        $dienthoai = $request->dienthoai;
        $trinhdochinhtri = $request->trinhdochinhtri;
        $trinhdochuyenmon = $request->trinhdochuyenmon;
        $trinhdohocvan = $request->trinhdohocvan;
        $chucvu = $request->chucvu;
        $donvi = $request->donvi;
        $vaitro = $request->vaitro;
        $quequan = $request->quequan;
        $nghenghiepchucvu = $request->nghenghiepchucvu;
        $avatar = $request->avatar;
        $currentUser = Auth::user();
        $existUserPhone = User::where('phone', $dienthoai)->first();
        $existUserUserName = User::where('username', $tendangnhap)->first();
        if ($existUserPhone != null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đã tồn tại người dùng với số điện thoại '.$dienthoai
            ], 200);
        }
        if ($existUserUserName != null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đã tồn tại người dùng với tên đăng nhập '.$tendangnhap
            ], 200);
        }
        else {
            $urlavatar = null;
            if ($avatar != null) {
                list($extension, $content) = explode(';', $avatar);
                $tmpExtension = explode('/', $extension);
                $fileName = sprintf('avatar_%s_%s.%s', $dienthoai, date('YmdHis'), $tmpExtension[1]);
                $content = explode(',', $content)[1];
                $urlavatar = FileHelper::SaveAvatar($content, $currentUser->tenant_id, $fileName);
            }
            DB::beginTransaction();
            try {
                DB::table('users')
                ->insert([
                    'id' => 0,
                    'role_id' => $vaitro,
                    'name' => $hoten,
                    'email' => $dienthoai,
                    'avatar' => $urlavatar,
                    'email_verified_at' => null,
                    'password' => Hash::make("abc123"),
                    'remember_token' => null,
                    'settings' => '{"locale":"vi"}',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => null,
                    'deleted_at' => null,
                    'created_by' => $currentUser->id,
                    'updated_by' => null,
                    'deleted_by' => null,
                    'username' => $dienthoai,
                    'ngay_sinh' => $ngaysinh,
                    'gioi_tinh' => $gioitinh,
                    'que_quan' => $quequan,
                    'dantoc_id' => $dantoc,
                    'phone' => $dienthoai,
                    'donvi_id' => $donvi,
                    'chucvu_id' => $chucvu,
                    'trinhdo_hocvan' => $trinhdohocvan,
                    'trinhdo_chinhtri' => $trinhdochinhtri,
                    'trinhdo_chuyenmon' => $trinhdochuyenmon,
                    'nghenghiep_chucvu' => $nghenghiepchucvu,
                    'is_active' => TrangThaiHoatDongEnum::HoatDong,
                    'tenant_id' => $currentUser->tenant_id,
                    'is_admin' => 0,
                    'tongiao' => $tongiao
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
    }

    public function UpdateUser(Request $request) {
        $userid = $request->userid;
        $hoten = $request->hoten;
        $tendangnhap = $request->tendangnhap;
        $ngaysinh = $request->ngaysinh;
        $ngaysinh = $ngaysinh != null ? DateTime::createFromFormat('Y-m-d', $ngaysinh) : $ngaysinh;
        $gioitinh = $request->gioitinh;
        $dantoc = $request->dantoc;
        $tongiao = $request->tongiao;
        $dienthoai = $request->dienthoai;
        $trinhdochinhtri = $request->trinhdochinhtri;
        $trinhdochuyenmon = $request->trinhdochuyenmon;
        $trinhdohocvan = $request->trinhdohocvan;
        $chucvu = $request->chucvu;
        $donvi = $request->donvi;
        $vaitro = $request->vaitro;
        $quequan = $request->quequan;
        $nghenghiepchucvu = $request->nghenghiepchucvu;
        $avatar = $request->avatar;
        $currentUser = Auth::user();
        $existUserPhone = User::where('id','!=', $userid)
                        ->where('phone', $dienthoai)->first();
        $existUserUserName = User::where('id','!=', $userid)
            ->where('username', $tendangnhap)->first();
        $user = User::find($userid);
        if ($user == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Người dùng không tồn tại'
            ], 200);
        }
        if ($existUserPhone != null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đã tồn tại người dùng với số điện thoại '.$dienthoai
            ], 200);
        }
        if ($existUserUserName != null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Đã tồn tại người dùng với tên đăng nhập '.$tendangnhap
            ], 200);
        }
        else {
            $urlavatar = null;
            // Avatar gửi lên null
            if ($avatar == null || $avatar == " " || $avatar == "") {
                // Nếu avatar đã tồn tại => Xóa avatar đã tồn tại
                if ($user->avatar != null) {
                    FileHelper::Remove('/storage'.$user->avatar);
                }
            }
            // Avatar gửi lên khác null
            else {
                // nếu avatar không là link uploads (base64) => xóa avatar đã tồn tại + update avatar mới
                if (!str_contains($avatar, 'uploads') && !str_contains($avatar, 'storage')) {
                    // Nếu đã tồn tại avatar => Xóa avatar đã tồn tại
                    if ($user->avatar != null) {
                        FileHelper::Remove('/storage'.$user->avatar);
                    }
                    list($extension, $content) = explode(';', $avatar);
                    $tmpExtension = explode('/', $extension);
                    $fileName = sprintf('avatar_%s_%s.%s', $dienthoai, date('YmdHis'), $tmpExtension[1]);
                    $content = explode(',', $content)[1];
                    $urlavatar = FileHelper::SaveAvatar($content, $currentUser->tenant_id, $fileName);
                } else {
                    // nếu avatar là link storage => link avatar đã lưu => ko xử lí gì thêm
                    $urlavatar = $user->avatar;
                }

            }
            DB::beginTransaction();
            try {
                DB::table('users')
                ->where('id', $userid)
                ->update([
                    'role_id' => $vaitro,
                    'name' => $hoten,
                    'email' => $dienthoai,
                    'avatar' => $urlavatar,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => $currentUser->id,
                    'username' => $tendangnhap,
                    'ngay_sinh' => $ngaysinh,
                    'gioi_tinh' => $gioitinh,
                    'que_quan' => $quequan,
                    'dantoc_id' => $dantoc,
                    'phone' => $dienthoai,
                    'donvi_id' => $donvi,
                    'chucvu_id' => $chucvu,
                    'trinhdo_hocvan' => $trinhdohocvan,
                    'trinhdo_chinhtri' => $trinhdochinhtri,
                    'trinhdo_chuyenmon' => $trinhdochuyenmon,
                    'nghenghiep_chucvu' => $nghenghiepchucvu,
                    'tongiao' => $tongiao
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
    }

    public function PartialViewEditUserModal(Request $request) {
        $currentUser = Auth::user();
        $userid = $request->userid;
        $user = User::find($userid);
        $donvis = DonVi::where('status', TrangThaiHoatDongEnum::HoatDong)->get();
        $dantocs = DanToc::where('status', TrangThaiHoatDongEnum::HoatDong)->get();
        $roles = Role::where('tenant_id', $currentUser->tenant_id)->get();
        $chucvus = ChucVu::where('status', TrangThaiHoatDongEnum::HoatDong)->get();
        $view = 'voyager::users.partials.sua-user-modal';
        return Voyager::view($view,compact('donvis', 'dantocs', 'roles', 'chucvus', 'user'));
    }

    public function UpdateTrangThaiUser(Request $request) {
        $userid = $request->userid;
        $user = User::find($userid);
        if ($user == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Người dùng không tồn tại'
            ], 200);
        }
        $currentUser = Auth::user();
        DB::beginTransaction();
        try {
            DB::table('users')
            ->where('id', $userid)
            ->update([
                'is_active' => $user->is_active == TrangThaiHoatDongEnum::HoatDong ? TrangThaiHoatDongEnum::KhongHoatDong : TrangThaiHoatDongEnum::HoatDong,
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

    public function DeleteUser(Request $request) {
        $userid = $request->userid;
        $user = User::find($userid);
        if ($user == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Người dùng không tồn tại'
            ], 200);
        }
        $currentUser = Auth::user();
        DB::beginTransaction();
        try {
            DB::table('users')
            ->where('id', $userid)
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
    public function ResetPassword(Request $request) {
        $userid = $request->userid;
        $user = User::find($userid);
        if ($user == null) {
            return response()->json([
                'error_code' => 1,
                'message' => 'Người dùng không tồn tại'
            ], 200);
        }
        $currentUser = Auth::user();
        DB::beginTransaction();
        try {
            DB::table('users')
            ->where('id', $userid)
            ->update([
                'password' => Hash::make("abc123"),
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

    public static function GetDisplayTTNguoiDung($trangthai) {
        $result = "";
        if ($trangthai == 1) {
            $result = "Hoạt động";
        } else {
            $result = "Không hoạt động";
        }
        return $result;
    }
    public static function GetClassTTNguoiDung($trangthai) {
        $class = "";
        if ($trangthai == 1) {
            $class = "tt-hoatdong";
        } else {
            $class = "tt-khonghoatdong";
        }
        return $class;
    }
}
