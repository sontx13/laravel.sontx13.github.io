<?php


namespace App\Models;

use App\Traits\Multitenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use Multitenant;
    use SoftDeletes;
}
