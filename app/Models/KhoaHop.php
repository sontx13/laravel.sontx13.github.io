<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KhoaHop extends BaseModel
{
    use SoftDeletes;
    protected $table = 'khoa_hop';

    public function kyhops() {
        return $this->hasMany(KyHop::class, 'khoa_hop_id', 'id');
    }
}
