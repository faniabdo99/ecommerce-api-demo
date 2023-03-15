<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     *========================= RELATIONS =========================
     */
    public function User(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function Store(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function Locale(): \Illuminate\Database\Eloquent\Relations\HasOne {
        return $this->hasOne(ProductLocale::class);
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
