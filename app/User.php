<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return string
     * @description Generate a random but static token to use in authentication, which can be decoded later to get the user object
     */
    public function getTokenAttribute(): string
    {
        return base64_encode("['email' => $this->email, 'id' => $this->id]");
    }

    public function Store(){
        return $this->hasOne(Store::class);
    }
    public function hasStore(){
        return $this->Store;
    }
}
