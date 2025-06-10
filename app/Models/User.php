<?php

namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use  HasFactory, Notifiable;

   protected $fillable = [
        'name', 
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sentFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(FriendRequest::class, 'recipient_id');
    }

    public function friendsOfMine()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id');
    }

    public function friendOf()
    {
        return $this->belongsToMany(User::class, 'friends', 'friend_id', 'user_id');
    }

    public function getFriendsAttribute()
    {
        $friends = $this->friendsOfMine->merge($this->friendOf);
        // dd('User ' . $this->name . ' (ID: ' . $this->id . ') has friends: ' . $friends->pluck('name')->implode(', ')); 
        return $friends;
    }

    
    public function isFriendWith(User $user)
    {
        $isFriend = $this->getFriendsAttribute()->contains($user);
        // dd($this->name . ' (ID: ' . $this->id . ') is friend with ' . $user->name . ' (ID: ' . $user->id . '): ' . ($isFriend ? 'Yes' : 'No')); 
        return $isFriend;
    }
}
