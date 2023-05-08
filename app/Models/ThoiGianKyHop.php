<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThoiGianKyHop extends Model
{
    use SoftDeletes;
    protected $table = 'thoigian_kyhop';

    public function lichhop() {
        return $this->hasMany(LichHop::class, 'thoigian_kyhop_id', 'id')->orderBy('buoi_hop');
    }

    public function lich_hop() {
        return $this->hasMany(LichHop::class, 'thoigian_kyhop_id', 'id')
            ->where('phonghop_id', '!=', 0)
            ->with('phonghop')
            ->orderBy('buoi_hop')
            ->select([
                'id',
                'buoi_hop',
                'phonghop_id',
                'noi_dung',
                'thoigian_batdau',
                'thoigian_ketthuc',
                'thoigian_kyhop_id'
            ]);
    }
}
