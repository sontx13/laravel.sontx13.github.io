<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatVan extends Model
{
    use SoftDeletes;
    protected $table = 'chat_van';
    public function traloichatvan() {
        return $this->belongsTo(TraloiChatvan::class, 'traloi_chatvan_id');
    }
    public function user() {
        return $this->hasOne(User::class, 'id', 'nguoi_chatvan');
    }
}
