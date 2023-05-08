<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiemDanh extends Model
{
    use SoftDeletes;
    protected $table = 'diem_danh';
}
