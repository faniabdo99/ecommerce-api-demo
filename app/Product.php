<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     *========================= RELATIONS =========================
     */
    public function User(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function Store(){
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     *========================= SCOPES =========================
     */
    public function scopeMine($query){
        return $query->where('user_id', auth()->user()->id);
    }
    /**
     *========================= CUSTOM ATTRIBUTES =========================
     */
    public function getVatValueAttribute(): float {
        return round($this->price * ($this->vat_percentage / 100));
    }
    public function getItemPriceAttribute(): float {
        // If VAT Included, basically do nothing
        if($this->is_vat_included){
            return round($this->price - $this->vat_value);
        }else{
            return round($this->price);
        }
    }
    public function getTotalPriceAttribute(): float {
        return round($this->item_price + $this->vat_value);
    }


}
