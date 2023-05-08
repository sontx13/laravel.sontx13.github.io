<?php

use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->to('/admin');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    // Khóa họp
    // View danh sách khóa họp
    Route::get('khoa-hop', 'App\Http\Controllers\Voyager\KhoaHopController@Index')->name('voyager.khoa-hop.index');
    //Partials List khóa họp
    Route::post('khoa-hop/getlistkhoahop', 'App\Http\Controllers\Voyager\KhoaHopController@Getlistkhoahop')->name('voyager.khoa-hop.getlistkhoahop');
    //Thêm mới khóa họp
    Route::post('khoa-hop/them-moi', 'App\Http\Controllers\Voyager\KhoaHopController@ThemMoiKhoaHop');
    //Sửa khóa họp
    Route::post('khoa-hop/cap-nhat', 'App\Http\Controllers\Voyager\KhoaHopController@CapNhatKhoaHop');
    //Xóa khóa họp
    Route::post('khoa-hop/xoa', 'App\Http\Controllers\Voyager\KhoaHopController@XoaKhoaHop');
    //Chuyển trạng thái khóa họp
    Route::post('khoa-hop/chuyentrangthai', 'App\Http\Controllers\Voyager\KhoaHopController@ChuyenTrangThai');
    // Partial view modal đại biểu khóa họp
    Route::post('khoa-hop/modal-dai-bieu', 'App\Http\Controllers\Voyager\KhoaHopController@PartialViewModalDsDaiBieu');
    // Partial view danh sách đại biểu khóa họp
    Route::post('khoa-hop/ds-daibieu', 'App\Http\Controllers\Voyager\KhoaHopController@PartialViewDsDaiBieu');
    // Danh sách user theo đơn vị id
    Route::get('nguoi-dung', 'App\Http\Controllers\Voyager\KhoaHopController@GetNguoiDungs');
    // Thêm đại biểu khóa họp
    Route::post('khoa-hop/add-daibieu', 'App\Http\Controllers\Voyager\KhoaHopController@AddDaiBieuKhoaHop');
    // Xóa đại biểu khóa họp
    Route::post('khoa-hop/xoa-daibieu', 'App\Http\Controllers\Voyager\KhoaHopController@XoaDaiBieuKhoaHop');
    // Xóa đại biểu khóa họp
    Route::post('khoa-hop/sapxep-daibieu', 'App\Http\Controllers\Voyager\KhoaHopController@SapXepBieuKhoaHop');

    //----Kỳ họp------------
    // View index kỳ họp
    Route::get('ky-hop', 'App\Http\Controllers\Voyager\KyHopController@Index')->name('voyager.ky-hop.index');
    // View danh sách kỳ họp
    Route::post('ky-hop/danh-sach', 'App\Http\Controllers\Voyager\KyHopController@PartialViewDsKyHop');
    // View thêm mới kỳ họp
    Route::get('ky-hop/{khoahopid}/create', 'App\Http\Controllers\Voyager\KyHopController@ViewCreate')->name('voyager.ky-hop.add');
    // Action thêm mới kỳ họp
    Route::post('ky-hop/them-moi', 'App\Http\Controllers\Voyager\KyHopController@CreateKyHop');
    // Partial view modal sửa kỳ họp
    Route::post('ky-hop/modal-sua', 'App\Http\Controllers\Voyager\KyHopController@PartialViewSuaKyHop');
    // Action cập nhật kỳ họp
    Route::post('ky-hop/cap-nhat', 'App\Http\Controllers\Voyager\KyHopController@UpdateKyHop');
    // Action xóa kỳ họp
    Route::post('ky-hop/xoa', 'App\Http\Controllers\Voyager\KyHopController@XoaKyHop');


    // Partial view modal lịch họp
    Route::post('ky-hop/lich-hop', 'App\Http\Controllers\Voyager\KyHopController@PartialViewSuaLichHop');
    // Action cập nhật lịch họp
    Route::post('lich-hop/cap-nhat', 'App\Http\Controllers\Voyager\KyHopController@UpdateLichHop');
    // Partial view modal sửa vai trò kỳ họp
    Route::post('ky-hop/vai-tro', 'App\Http\Controllers\Voyager\KyHopController@PartialViewSuaVaiTroKyHop');
    // Partial view danh sách đại biểu kỳ họp
    Route::post('ky-hop/dai-bieu', 'App\Http\Controllers\Voyager\KyHopController@PartialViewDanhSachDaiBieu');
    // Action thêm tất cả đại biểu của khóa họp
    Route::post('/ky-hop/them-tatca-daibieu', 'App\Http\Controllers\Voyager\KyHopController@ThemTatCaDaiBieuKhoaHop');
    // Action xóa vai trò đại biểu
    Route::post('/ky-hop/xoa-vaitro-daibieu', 'App\Http\Controllers\Voyager\KyHopController@XoaVaiTroDaiBieu');
    // Action cập nhật chủ tọa
    Route::post('/ky-hop/capnhat-chutoa', 'App\Http\Controllers\Voyager\KyHopController@CapNhatChuToa');
    // Partial view combobox đại biểu kỳ họp
    Route::post('ky-hop/cbx-daibieu', 'App\Http\Controllers\Voyager\KyHopController@PartialViewComBoBoxDaiBieu');
    // Action thêm đại biểu
    Route::post('/ky-hop/them-daibieu', 'App\Http\Controllers\Voyager\KyHopController@ThemDaiBieu');
    // Partial view combobox khách mời kỳ họp
    Route::post('ky-hop/cbx-khachmoi', 'App\Http\Controllers\Voyager\KyHopController@PartialViewComBoBoxKhachMoi');
    // Action thêm khách mời
    Route::post('/ky-hop/them-khachmoi', 'App\Http\Controllers\Voyager\KyHopController@ThemKhachMoi');
    //Partials List kỳ họp theo ID khóa họp
    Route::post('ky-hop/getlistkyhopbyidkhoahop', 'App\Http\Controllers\Voyager\KyHopController@Getlistkyhopbyidkhoahop')->name('voyager.ky-hop.getlistkyhopbyidkhoahop');
    // Lấy ds kỳ họp theo khóa họp
    Route::get('ky-hops', 'App\Http\Controllers\Voyager\KyHopController@GetKyHops');
    // Lấy ds đại biểu theo kỳ họp
    Route::get('ky-hop/dai-bieu', 'App\Http\Controllers\Voyager\KyHopController@DsDaiBieus');
    // Action cập nhật lại số thứ tự theo đại biểu khóa họp
    Route::post('/ky-hop/capnhat-stt', 'App\Http\Controllers\Voyager\KyHopController@ReloadSTT');

    // Nghiệp vụ điểm danh
    // View index điểm danh
    Route::get('diem-danh', 'App\Http\Controllers\Voyager\DiemDanhController@Index')->name('voyager.diem-danh.index');
    // Partial view buổi họp
    Route::post('diem-danh/buoi-hop', 'App\Http\Controllers\Voyager\DiemDanhController@PartialViewDsBuoiHop');
    // Partial view điểm danh buổi họp
    Route::post('diem-danh/diemdanh-buoihop', 'App\Http\Controllers\Voyager\DiemDanhController@PartialViewDiemDanh');
    // Action điểm danh thủ công
    Route::post('diem-danh/luu-thu-cong', 'App\Http\Controllers\Voyager\DiemDanhController@LuuThuCong');
    // Partial view trình chiếu điểm danh
    Route::get('diem-danh/trinhchieu/{kyhopid}', 'App\Http\Controllers\Voyager\DiemDanhController@PartialViewTrinhChieu');
    // Danh sách điểm danh
    Route::post('diem-danh/danh-sach', 'App\Http\Controllers\Voyager\DiemDanhController@DanhSachDiemDanh');

    // Nghiệp vụ biểu quyết
    // View index biểu quyết
    Route::get('bieu-quyet', 'App\Http\Controllers\Voyager\BieuQuyetController@Index')->name('voyager.bieu-quyet.index');
    // Partial view buổi họp
    Route::post('bieu-quyet/danh-sach', 'App\Http\Controllers\Voyager\BieuQuyetController@PartialViewDsBieuQuyet');
    // Action thêm mới biểu quyết
    Route::post('bieu-quyet/them-moi', 'App\Http\Controllers\Voyager\BieuQuyetController@ThemMoiBieuQuyet');
    // Action cập nhật biểu quyết
    Route::post('bieu-quyet/cap-nhat', 'App\Http\Controllers\Voyager\BieuQuyetController@CapNhatBieuQuyet');
    // Action xóa biểu quyết
    Route::post('bieu-quyet/xoa', 'App\Http\Controllers\Voyager\BieuQuyetController@XoaBieuQuyet');
    // Action bắt đầu biểu quyết
    Route::post('bieu-quyet/batdau', 'App\Http\Controllers\Voyager\BieuQuyetController@BatDauBieuQuyet');
    // Action kết thúc biểu quyết
    Route::post('bieu-quyet/ketthuc', 'App\Http\Controllers\Voyager\BieuQuyetController@KetThucBieuQuyet');
    // Partial view trình chiếu biểu quyết
    Route::get('bieu-quyet/{kyhopid}/trinhchieu/{id}', 'App\Http\Controllers\Voyager\BieuQuyetController@PartialViewTrinhChieu');
    // Lấy kết quả của biểu quyết
    Route::post('bieu-quyet/ket-qua', 'App\Http\Controllers\Voyager\BieuQuyetController@KetQuaBieuQuyet');

    // Nghiệp vụ chất vấn
    // View index chất vấn
    Route::get('chat-van', 'App\Http\Controllers\Voyager\ChatVanController@Index')->name('voyager.chat-van.index');
    // Partial view ngày họp
    Route::post('chat-van/ngay-hop', 'App\Http\Controllers\Voyager\ChatVanController@PartialViewDsNgayHop');
    // Partial view chất vấn ngày họp
    Route::post('chat-van/chatvan-ngayhop', 'App\Http\Controllers\Voyager\ChatVanController@PartialViewChatVan');
    // Action thêm mới phiên chất vấn
    Route::post('phien-chat-van/them-moi', 'App\Http\Controllers\Voyager\ChatVanController@ThemMoiPhienChatVan');
    // Action cập nhật phiên chất vấn
    Route::post('phien-chat-van/cap-nhat', 'App\Http\Controllers\Voyager\ChatVanController@CapNhatPhienChatVan');
    // Action xóa phiên chất vấn
    Route::post('phien-chat-van/xoa', 'App\Http\Controllers\Voyager\ChatVanController@XoaPhienChatVan');
    // Action bắt đầu phiên chất vấn
    Route::post('phien-chat-van/batdau', 'App\Http\Controllers\Voyager\ChatVanController@BatDauPhienChatVan');
    // Action kết thúc phiên chất vấn
    Route::post('phien-chat-van/ketthuc', 'App\Http\Controllers\Voyager\ChatVanController@KetThucPhienChatVat');
    // Partial view trình chiếu chất vấn
    Route::get('chat-van/{kyhopid}/trinhchieu/{id}', 'App\Http\Controllers\Voyager\ChatVanController@PartialViewTrinhChieu');

    //---------Chất vấn---------------------------------------
    //---------Partial view-----------------------------------
    //---------Modal thêm, sửa đại biểu chất vấn--------------
    Route::post('chat-van/modal-themmoi', 'App\Http\Controllers\Voyager\ChatVanController@PartialViewThemMoiDaiBieuChatVan');
    // Action thêm mới đại biểu chất vấn
    Route::post('chat-van/them-moi', 'App\Http\Controllers\Voyager\ChatVanController@ThemMoiDaiBieuChatVan');
    // Action cập nhật đại biểu chất vấn
    Route::post('chat-van/cap-nhat', 'App\Http\Controllers\Voyager\ChatVanController@CapNhatDaiBieuChatVan');
    // Action duyệt đại biểu chất vấn
    Route::post('chat-van/duyet', 'App\Http\Controllers\Voyager\ChatVanController@DuyetDaiBieuChatVan');
    // Action xóa đại biểu chất vấn
    Route::post('chat-van/xoa', 'App\Http\Controllers\Voyager\ChatVanController@XoaDaiBieuChatVan');
    // Action bắt đầu đại biểu chất vấn
     Route::post('chat-van/bat-dau', 'App\Http\Controllers\Voyager\ChatVanController@BatDauDaiBieuChatVan');
     // Action kết thúc đại biểu chất vấn
     Route::post('chat-van/ket-thuc', 'App\Http\Controllers\Voyager\ChatVanController@KetThucDaiBieuChatVat');

    //---------Tài liệu---------------------------------------
    Route::get('tai-lieu', 'App\Http\Controllers\Voyager\TaiLieuController@Index')->name('voyager.tai-lieu.index');
    // Dự thảo nghị quyết
    Route::get('duthao-nghiquyet', 'App\Http\Controllers\Voyager\TaiLieuController@Index')->name('voyager.duthao-nghiquyet.index');
    // Nghị quyết
    Route::get('nghi-quyet', 'App\Http\Controllers\Voyager\TaiLieuController@Index')->name('voyager.nghi-quyet.index');
    // Partial view buổi họp
    Route::post('tai-lieu/danh-sach', 'App\Http\Controllers\Voyager\TaiLieuController@PartialViewDsTaiLieu');
    // Action tạo thư mục
    Route::post('tai-lieu/tao-thu-muc', 'App\Http\Controllers\Voyager\TaiLieuController@TaoThuMuc');
    // Partial view folder
    Route::post('tai-lieu/xem-thu-muc', 'App\Http\Controllers\Voyager\TaiLieuController@PartialViewFolder');
    // Action cập nhật thư mục
    Route::post('tai-lieu/cap-nhat-thu-muc', 'App\Http\Controllers\Voyager\TaiLieuController@CapNhatThuMuc');
    // Action upload files
    Route::post('tai-lieu/upload', 'App\Http\Controllers\Voyager\TaiLieuController@Upload')->name('voyager.tai-lieu.upload');
    // Partial view file
    Route::post('tai-lieu/xem-file', 'App\Http\Controllers\Voyager\TaiLieuController@PartialViewFile');
    // Action cập nhật file
    Route::post('tai-lieu/cap-nhat-file', 'App\Http\Controllers\Voyager\TaiLieuController@CapNhatFile');
    // Action xóa file đính kèm
    Route::post('tai-lieu/xoa-file-dinhkem', 'App\Http\Controllers\Voyager\TaiLieuController@XoaFileDinhKem');
    // Action upload file đính kèm
    Route::post('tai-lieu/upload-file-attachment', 'App\Http\Controllers\Voyager\TaiLieuController@UploadFileDinhKem');
    // Action upload file đính kèm
    Route::post('tai-lieu/xoa-tailieu', 'App\Http\Controllers\Voyager\TaiLieuController@XoaTaiLieu');

    // Quản lý user
    Route::get('users', 'App\Http\Controllers\Voyager\UserController@Index')->name('voyager.users.index');
    // Partial view danh sách user
    Route::post('user/danh-sach', 'App\Http\Controllers\Voyager\UserController@PartialViewDsUser');
    // Thêm mới user
    Route::post('user/them-moi', 'App\Http\Controllers\Voyager\UserController@CreateUser');
    // Sửa user
    Route::post('user/sua-modal', 'App\Http\Controllers\Voyager\UserController@PartialViewEditUserModal');
    // Cập nhật user
    Route::post('user/cap-nhat', 'App\Http\Controllers\Voyager\UserController@UpdateUser');
    // Chuyển trạng thái người dùng
    Route::post('user/chuyen-trangthai', 'App\Http\Controllers\Voyager\UserController@UpdateTrangThaiUser');
    // Xóa người dùng
    Route::post('user/xoa', 'App\Http\Controllers\Voyager\UserController@DeleteUser');
    // Reset password
    Route::post('user/resetpassword', 'App\Http\Controllers\Voyager\UserController@ResetPassword');
});

Route::get('login', ['uses' => 'App\Http\Controllers\Voyager\VoyagerAuthController@login', 'as' => 'login']);
