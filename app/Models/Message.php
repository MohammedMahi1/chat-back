<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $guarded = [];

    public function conversation(){
        return $this->belongsTo(Conversation::class,"conversation_id","id");
    }

    public function user(){
        return $this->belongsTo(User::class)->withDefault([
            "name"=> _('User'),
        ]);
    }

    public function recrpients(){
        return $this->belongsToMany(User::class,"recipients")->withPivot([
            "read_at"=>"deleted_at"
        ]);
    }
}
