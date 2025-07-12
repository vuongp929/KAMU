<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value'];

    // Mỗi giá trị thuộc về một thuộc tính
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    // Giá trị này được sử dụng trong nhiều biến thể
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_attribute_value')
            ->withTimestamps();
    }
}
