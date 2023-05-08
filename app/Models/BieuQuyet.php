<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BieuQuyet extends Model
{
    use SoftDeletes;
    protected $table = 'bieu_quyet';

    public function ketquachitiets() {
        return $this->hasMany(KetquaBieuquyetChitiet::class, 'bieuquyet_id', 'id');
    }

    public function ketqua() {
        return $this->hasOne(KetquaBieuquyet::class, 'bieuquyet_id', 'id');
    }
}
