<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DaibieuKhoahop extends Model
{
    protected $table = 'daibieu_khoahop';

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function khoahop() {
        return $this->hasOne(KhoaHop::class, 'id', 'khoahop_id');
    }
}
