<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class KyHop extends BaseModel
{
    use SoftDeletes;
    protected $table = 'ky_hop';

    public function khoahop() {
        return $this->belongsTo(KhoaHop::class, 'khoa_hop_id');
    }

    public function thoigian() {
        return $this->hasMany(ThoiGianKyHop::class, 'kyhop_id', 'id')->orderBy('ngay_dienra');
    }
}
