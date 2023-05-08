<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class TaiLieu extends BaseModel
{
    use SoftDeletes;
    protected $table = 'tai_lieu';

    public function tailieuchitiet() {
        return $this->hasOne(TaiLieuChiTiet::class, 'tailieu_id', 'id');
    }
}
