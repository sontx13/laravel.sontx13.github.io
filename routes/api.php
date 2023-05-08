<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'App\Http\Controllers\AuthController@login');
  	Route::post('refreshtoken', 'App\Http\Controllers\AuthController@getTokenAndRefreshTokenByRefreshToken');
    Route::post('forgetpassword', 'App\Http\Controllers\AuthController@forgetPassword');
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('logout', 'App\Http\Controllers\AuthController@logout');
        Route::post('user', 'App\Http\Controllers\AuthController@user');
        Route::post('changepassword', 'App\Http\Controllers\AuthController@changePassword');
        Route::post('getinfo', 'App\Http\Controllers\AuthController@getInfors');
    });
});

// Điểm danh
Route::group([
    'prefix' => 'diemdanh'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('set', 'App\Http\Controllers\DiemDanhApiController@SetDiemDanh');
    });
});

// Biểu quyết
Route::group([
    'prefix' => 'bieuquyet'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('get', 'App\Http\Controllers\BieuQuyetApiController@getBieuQuyet');
        Route::post('set', 'App\Http\Controllers\BieuQuyetApiController@setBieuQuyet');
    });
});

Route::get('time', 'App\Http\Controllers\BieuQuyetApiController@getTime');

// Tài liệu
Route::group([
    'prefix' => 'tailieu'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('danh-sach', 'App\Http\Controllers\TaiLieuApiController@GetDsTaiLieu');
        Route::post('tim-kiem', 'App\Http\Controllers\TaiLieuApiController@SearchTaiLieu');
    });
});

// Kỳ họp
Route::group([
    'prefix' => 'kyhop'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('danh-sach', 'App\Http\Controllers\KyHopApiController@GetDsKyHops');
    });
});

// Dự thảo nghị quyết
Route::group([
    'prefix' => 'duthaonghiquyet'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('danh-sach', 'App\Http\Controllers\DuThaoNghiQuyetApiController@GetDsDuThaoNghiQuyet');
        Route::post('tim-kiem', 'App\Http\Controllers\DuThaoNghiQuyetApiController@SearchDuThaoNghiQuyet');
    });
});

// Nghị quyết thông qua
Route::group([
    'prefix' => 'nghiquyet'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('danh-sach', 'App\Http\Controllers\NghiQuyetApiController@GetDsNghiQuyet');
        Route::post('tim-kiem', 'App\Http\Controllers\NghiQuyetApiController@SearchNghiQuyet');
    });
});

// Đại biểu
Route::group([
    'prefix' => 'daibieu'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('danh-sach', 'App\Http\Controllers\DaiBieuApiController@GetDsDaiBieu');
        Route::post('tim-kiem', 'App\Http\Controllers\DaiBieuApiController@SearchDaiBieu');
        Route::post('doi-matkhau', 'App\Http\Controllers\DaiBieuApiController@ChangePassword');
    });
});

// Chất vấn
Route::group([
    'prefix' => 'chatvan'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
    Route::post('danhsach-phien', 'App\Http\Controllers\ChatVanApiController@GetDsNguoiTraLoi');
    Route::post('danhsach-chatvan', 'App\Http\Controllers\ChatVanApiController@GetDsChatVan');

    // Nghiệp vụ chất vấn
    // Action thêm mới phiên chất vấn
    // // Route::post('themmoi-phien', 'App\Http\Controllers\ChatVanApiController@ThemMoiPhienChatVan');
    // // // Action cập nhật phiên chất vấn
    // // Route::post('capnhat-phien', 'App\Http\Controllers\ChatVanApiController@CapNhatPhienChatVan');
    // // // Action xóa phiên chất vấn
    // // Route::post('xoa-phien', 'App\Http\Controllers\ChatVanApiController@XoaPhienChatVan');
    // // // Action bắt đầu phiên chất vấn
    // // Route::post('batdau-phien', 'App\Http\Controllers\ChatVanApiController@BatDauPhienChatVan');
    // // // Action kết thúc phiên chất vấn
    // // Route::post('ketthuc-phien', 'App\Http\Controllers\ChatVanApiController@KetThucPhienChatVat');

    //---------Chất vấn---------------------------------------
    // Action thêm mới đại biểu chất vấn
    Route::post('themmoi', 'App\Http\Controllers\ChatVanApiController@ThemMoiDaiBieuChatVan');
    // Action cập nhật đại biểu chất vấn
    Route::post('capnhat', 'App\Http\Controllers\ChatVanApiController@CapNhatDaiBieuChatVan');
    // Action duyệt đại biểu chất vấn
    Route::post('duyet', 'App\Http\Controllers\ChatVanApiController@DuyetDaiBieuChatVan');
    // Action xóa đại biểu chất vấn
    Route::post('xoa', 'App\Http\Controllers\ChatVanApiController@XoaDaiBieuChatVan');
    // Action bắt đầu đại biểu chất vấn
     Route::post('batdau', 'App\Http\Controllers\ChatVanApiController@BatDauDaiBieuChatVan');
     // Action kết thúc đại biểu chất vấn
     Route::post('ketthuc', 'App\Http\Controllers\ChatVanApiController@KetThucDaiBieuChatVat');
    });
});


// Lịch họp
Route::group([
    'prefix' => 'lichhop'
], function () {
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('danh-sach', 'App\Http\Controllers\KyHopApiController@GetDsLichHops');
    });
});
