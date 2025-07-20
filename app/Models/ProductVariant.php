<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_variants';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'product_id',
        'size',
        'price',
        'stock',
        'name',
    ];

    // Quan hệ với sản phẩm
      public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    // Quan hệ với chi tiết đơn hàng
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // Trỏ đến model ProductImage, sử dụng khóa ngoại 'product_variant_id'
        return $this->hasMany(ProductImage::class, 'product_variant_id');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_attribute_value');
    }
}
