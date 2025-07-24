<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',
    ];

    // Quan hệ với đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ với biến thể sản phẩm
   public function productVariant()
{
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
}


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
