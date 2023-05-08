<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaiBieuKyHop extends Model
{
    protected $table = 'daibieu_kyhop';

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function kyhop() {
        return $this->hasOne(KyHop::class, 'id', 'kyhop_id');
    }
}
