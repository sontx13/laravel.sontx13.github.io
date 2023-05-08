<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TraloiChatvan extends Model
{
    use SoftDeletes;
    protected $table = 'traloi_chatvan';
    public function chatvans() {
        //Sắp xếp theo trạng thái Đang chất vấn -> Chờ chất vấn -> Chờ duyệt -> Đã chất vấn
        return $this->hasMany(ChatVan::class, 'traloi_chatvan_id')->orderByRaw("FIELD(trang_thai , '2', '1', '0', '3') ASC")->orderBy('stt');
    }
}
