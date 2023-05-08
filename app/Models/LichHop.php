<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LichHop extends Model
{
    use SoftDeletes;
    protected $table = 'lich_hop';

    public function phong_hop() {
        return $this->hasOne(PhongHop::class, 'id', 'phonghop_id');
    }

    public function phonghop() {
        return $this->hasOne(PhongHop::class, 'id', 'phonghop_id')
            ->select([
                'id',
                'ten_phonghop',
                'dia_diem',
                'lat',
                'long'
            ]);
    }

    public function thoi_gian() {
        return $this->hasOne(ThoiGianKyHop::class, 'id', 'thoigian_kyhop_id');
    }
}
