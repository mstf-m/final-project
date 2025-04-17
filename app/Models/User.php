<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'user_id';
    protected $table = 'users';

    protected $fillable = [
        'firstname',
        'lastname',
        'phone_number',
        'password_hash',
        'avatar_url',
        'gender',
        'birthday',
        'bio'
    ];

    protected $hidden = ['password_hash'];

    public function activities()
    {
        return $this->hasMany(Activity::class, 'creator_id');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, 'user_id');
    }

    public function requests()
    {
        return $this->hasMany(ActivityRequest::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
}