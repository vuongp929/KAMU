<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductVariantAttributeValue extends Pivot
{
    protected $table = 'product_variant_attribute_value';

    // Nếu bạn có thêm các cột trong bảng pivot, có thể thêm chúng vào fillable:
    protected $fillable = ['product_variant_id', 'attribute_value_id'];
}
