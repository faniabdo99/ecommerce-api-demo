<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model{
    protected $guarded = [];

    public function User(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
