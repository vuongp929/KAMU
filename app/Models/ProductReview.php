<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'stars',
        'content',
        'parent_id',
        'is_hidden',
    ];

    // Người viết bình luận/đánh giá
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Sản phẩm được đánh giá
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Bình luận con (trả lời)
    public function replies()
    {
        return $this->hasMany(ProductReview::class, 'parent_id');
    }

    // Bình luận cha (nếu có)
    public function parent()
    {
        return $this->belongsTo(ProductReview::class, 'parent_id');
    }
}
