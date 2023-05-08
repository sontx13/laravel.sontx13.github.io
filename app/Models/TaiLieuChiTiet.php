<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class TaiLieuChiTiet extends BaseModel
{
    use SoftDeletes;
    protected $table = 'tailieu_chitiet';

    public function tailieu() {
        return $this->belongsTo(TaiLieu::class, 'tailieu_id');
    }
}
