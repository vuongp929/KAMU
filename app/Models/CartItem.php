<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',
        'price_at_order',
        'size',
    ];

    // ✅ Mỗi CartItem thuộc về một giỏ hàng
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id')->withTrashed();
    }
    public function getProductAttribute()
    {
        return $this->variant?->product;
    }
}
