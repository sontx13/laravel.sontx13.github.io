<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addYears(1));
        //---------------Quyền Khóa họp-------------------
        // Quyền xem khóa họp
        Gate::define('browse_khoa-hop', function ($user) {
            return $user->hasPermission('browse_khoa-hop');
        });
        // Quyền thêm khóa họp
        Gate::define('add_khoa-hop', function ($user) {
            return $user->hasPermission('add_khoa-hop');
        });
        // Quyền sửa khóa họp
        Gate::define('edit_khoa-hop', function ($user) {
            return $user->hasPermission('edit_khoa-hop');
        });
        // Quyền xóa khóa họp
        Gate::define('delete_khoa-hop', function ($user) {
            return $user->hasPermission('delete_khoa-hop');
        });
        // Quyền xem danh sách đại biểu
        Gate::define('browse_daibieu-khoahop', function ($user) {
            return $user->hasPermission('browse_daibieu-khoahop');
        });
        // Quyền thêm đại biểu khóa họp
        Gate::define('add_daibieu-khoahop', function ($user) {
            return $user->hasPermission('add_daibieu-khoahop');
        });
        // Quyền xóa đại biểu khóa họp
        Gate::define('delete_daibieu-khoahop', function ($user) {
            return $user->hasPermission('delete_daibieu-khoahop');
        });
        // Quyền xóa đại biểu khóa họp
        Gate::define('edit_daibieu-khoahop', function ($user) {
            return $user->hasPermission('edit_daibieu-khoahop');
        });
        //---------------End Quyền Khóa họp----------------

        //------------------ Quyền Kỳ họp -------------------
        // Quyền xem kỳ họp
        Gate::define('browse_ky-hop', function ($user) {
            return $user->hasPermission('browse_ky-hop');
        });
        // Quyền thêm kỳ họp
        Gate::define('add_ky-hop', function ($user) {
            return $user->hasPermission('add_ky-hop');
        });
        // Quyền sửa kỳ họp
         Gate::define('edit_ky-hop', function ($user) {
            return $user->hasPermission('edit_ky-hop');
        });
        // Quyền xóa kỳ họp
        Gate::define('delete_ky-hop', function ($user) {
            return $user->hasPermission('delete_ky-hop');
        });
        // Quyền xem lịch hop
        Gate::define('browse_lich-hop', function ($user) {
            return $user->hasPermission('browse_lich-hop');
        });
        // Quyền sửa lịch hop
        Gate::define('edit_lich-hop', function ($user) {
            return $user->hasPermission('edit_lich-hop');
        });
        // Quyền xem đại biểu kỳ họp
        Gate::define('browse_daibieu-kyhop', function ($user) {
            return $user->hasPermission('browse_daibieu-kyhop');
        });
        // Quyền sửa đại biểu kỳ họp
        Gate::define('edit_daibieu-kyhop', function ($user) {
            return $user->hasPermission('edit_daibieu-kyhop');
        });
        // Quyền xóa đại biểu kỳ họp
        Gate::define('delete_daibieu-kyhop', function ($user) {
            return $user->hasPermission('delete_daibieu-kyhop');
        });
        //---------------End Quyền Kỳ họp----------------

        //------------------ Quyền Điểm danh -------------------
        // Quyền xem điểm danh
        Gate::define('browse_diem-danh', function ($user) {
            return $user->hasPermission('browse_diem-danh');
        });
        // Quyền điểm danh thủ công
        Gate::define('edit_diem-danh', function ($user) {
            return $user->hasPermission('edit_diem-danh');
        });
        //---------------End Quyền Điểm danh ----------------
        //------------------ Quyền Biểu quyết -------------------
        Gate::define('browse_bieu-quyet', function ($user) {
            return $user->hasPermission('browse_bieu-quyet');
        });
        Gate::define('edit_bieu-quyet', function ($user) {
            return $user->hasPermission('edit_bieu-quyet');
        });
        Gate::define('delete_bieu-quyet', function ($user) {
            return $user->hasPermission('delete_bieu-quyet');
        });
        Gate::define('add_bieu-quyet', function ($user) {
            return $user->hasPermission('add_bieu-quyet');
        });
        //---------------End Quyền Biểu quyết ----------------
        //------------------ Quyền Chất vấn -------------------
        Gate::define('browse_chat-van', function ($user) {
            return $user->hasPermission('browse_chat-van');
        });
        Gate::define('add_chat-van', function ($user) {
            return $user->hasPermission('add_chat-van');
        });
        Gate::define('edit_chat-van', function ($user) {
            return $user->hasPermission('edit_chat-van');
        });
        Gate::define('delete_chat-van', function ($user) {
            return $user->hasPermission('delete_chat-van');
        });
        Gate::define('add_traloi-chatvan', function ($user) {
            return $user->hasPermission('add_chat-van');
        });
        Gate::define('edit_traloi-chatvan', function ($user) {
            return $user->hasPermission('edit_chat-van');
        });
        Gate::define('delete_traloi-chatvan', function ($user) {
            return $user->hasPermission('delete_chat-van');
        });
        //---------------End Quyền Chất vấn ----------------
        //------------------ Quyền Tài liệu -------------------
        Gate::define('browse_tai-lieu', function ($user) {
            return $user->hasPermission('browse_tai-lieu');
        });
        Gate::define('read_tai-lieu', function ($user) {
            return $user->hasPermission('read_tai-lieu');
        });
        Gate::define('add_tai-lieu', function ($user) {
            return $user->hasPermission('add_tai-lieu');
        });
        Gate::define('edit_tai-lieu', function ($user) {
            return $user->hasPermission('edit_tai-lieu');
        });
        Gate::define('delete_tai-lieu', function ($user) {
            return $user->hasPermission('delete_tai-lieu');
        });
        //------------------ End Quyền Tài liệu -------------------
        //------------------ Quyền Dự thảo nghị quyết -------------------
        Gate::define('browse_duthao-nghiquyet', function ($user) {
            return $user->hasPermission('browse_duthao-nghiquyet');
        });
        Gate::define('read_duthao-nghiquyet', function ($user) {
            return $user->hasPermission('read_duthao-nghiquyet');
        });
        Gate::define('add_duthao-nghiquyet', function ($user) {
            return $user->hasPermission('add_duthao-nghiquyet');
        });
        Gate::define('edit_duthao-nghiquyet', function ($user) {
            return $user->hasPermission('edit_duthao-nghiquyet');
        });
        Gate::define('delete_duthao-nghiquyet', function ($user) {
            return $user->hasPermission('delete_duthao-nghiquyet');
        });
        //------------------ End Quyền Dự thảo nghị quyết -------------------
        //------------------ Quyền Nghị quyết -------------------
        Gate::define('browse_nghi-quyet', function ($user) {
            return $user->hasPermission('browse_nghi-quyet');
        });
        Gate::define('read_nghi-quyet', function ($user) {
            return $user->hasPermission('read_nghi-quyet');
        });
        Gate::define('add_nghi-quyet', function ($user) {
            return $user->hasPermission('add_nghi-quyet');
        });
        Gate::define('edit_nghi-quyet', function ($user) {
            return $user->hasPermission('edit_nghi-quyet');
        });
        Gate::define('delete_nghi-quyet', function ($user) {
            return $user->hasPermission('delete_nghi-quyet');
        });
        //------------------ End Quyền Nghị quyết -------------------
    }
}
