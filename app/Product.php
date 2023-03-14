<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{
    protected $guarded = [];

    public function User(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function Store(){
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function scopeMine($query){
        return $query->where('user_id', auth()->user()->id);
    }
    public function scopeMyStore($query){
        return $query->where('store_id', auth()->user()->Store->id);
    }
}
