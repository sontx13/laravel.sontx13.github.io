<?php


namespace App\Models;

use App\Traits\Multitenant;
use Illuminate\Database\Eloquent\SoftDeletes;


class Post extends \TCG\Voyager\Models\Post
{
    use Multitenant;
    use SoftDeletes;
}
