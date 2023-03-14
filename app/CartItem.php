<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model {
    protected $guarded = [];

    public function Product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function scopeActive($query){
        return $query->where('status', 'active');
    }
}
