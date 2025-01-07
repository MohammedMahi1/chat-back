<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Conversation;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'image_profile',
        'image_url',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function conversation()
    {
        return $this->belongsToMany(Conversation::class, "users_id", "id")
        ->latest('last_message_id')
        ->withPivot([
            "role" => "joined_at",
        ]);
    }

    public function sendMessages()
    {
        return $this->hasMany(Message::class, "user_id", "id");
    }
    public function receivedMessages()
    {
        return $this->belongsToMany(Message::class,"recipients" )
        ->withPivot([
            "readt_at"=>"deleted_at",
        ]);
    }
}
