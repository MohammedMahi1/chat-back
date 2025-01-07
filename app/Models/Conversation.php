<?php

namespace App\Models;

use App\Models\Message;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    public $fillable = ["users_id","label"."last_message_id"];

    public function participants (){
        return $this->belongsToMany(User::class,"participants")
        ->withPivot(["joined_at","role"]);
    }

    public function message(){
        return $this->hasMany(Message::class,"conversation_id","id")->latest();
    }

    public function user (){
        return $this->belongsTo(User::class,"user_id","id");
    }

    public function lastMessage (){
        return $this->belongsTo(Message::class,"last_message_id","id")
        ->withDefault();
    }
}
