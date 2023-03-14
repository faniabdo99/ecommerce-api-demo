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

    public function getVatValueAttribute(){
        return round($this->price * ($this->vat_percentage / 100));
    }
    public function getItemPriceAttribute(){
        // If VAT Included, basically do nothing
        if($this->is_vat_included){
            return round($this->price - $this->vat_value);
        }else{
            return $this->price;
        }
    }
    public function getTotalPriceAttribute(){
        return round($this->item_price + $this->vat_value);
    }


}
