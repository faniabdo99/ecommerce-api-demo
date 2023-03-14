<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLocale extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    /**
     *========================= RELATIONS =========================
     */
    public function Product(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
