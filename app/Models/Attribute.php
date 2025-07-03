<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attribute extends Model
{
    protected $fillable = ['name'];

    // Một thuộc tính có nhiều giá trị
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    // Các biến thể sản phẩm liên quan đến thuộc tính này thông qua giá trị
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_attribute_value', 'attribute_value_id', 'product_variant_id')
            ->using(ProductVariantAttributeValue::class)
            ->withTimestamps();
    }
}
