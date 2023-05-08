<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KetquaBieuquyet extends Model
{
    protected $table = 'ketqua_bieuquyet';

    public function bieuquyet() {
        return $this->belongsTo(BieuQuyet::class, 'bieuquyet_id');
    }
}
