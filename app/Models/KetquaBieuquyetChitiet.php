<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KetquaBieuquyetChitiet extends Model
{
    protected $table = 'ketqua_bieuquyet_chitiet';

    public function bieuquyet() {
        return $this->belongsTo(BieuQuyet::class, 'bieuquyet_id');
    }
}
