<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     *========================= RELATIONS =========================
     */
    public function User(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
