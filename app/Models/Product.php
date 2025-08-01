<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Tên bảng trong database.
     * @var string
     */
    protected $table = 'products';

    /**
     * Các thuộc tính có thể gán hàng loạt.
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'image', // Cột này để lưu đường dẫn ảnh chính cho việc truy cập nhanh
    ];

    //======================================================================
    // MỐI QUAN HỆ (RELATIONSHIPS)
    //======================================================================

    /**
     * Lấy các danh mục của sản phẩm (Nhiều-Nhiều).
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    /**
     * Lấy tất cả các biến thể của sản phẩm (Một-Nhiều).
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Lấy các ảnh chung của sản phẩm (không bao gồm ảnh của biến thể).
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->whereNull('product_variant_id');
    }

    /**
     * Lấy ảnh được đánh dấu là ảnh chính (is_main = true).
     */
    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    /**
     * Lấy ảnh được upload đầu tiên.
     */
    public function firstImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->oldestOfMany();
    }
    
    /**
     * Lấy các cart items liên quan đến sản phẩm này.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Lấy các đánh giá/bình luận của sản phẩm.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(\App\Models\ProductReview::class);
    }

    //======================================================================
    // THUỘC TÍNH ẢO (ACCESSORS)
    //======================================================================

    /**
     * Lấy khoảng giá của sản phẩm dưới dạng chuỗi đã định dạng.
     * @return string
     */
    public function getPriceRangeAttribute(): string
    {
        if (!$this->relationLoaded('variants') || $this->variants->isEmpty()) {
            return "Chưa có giá";
        }

        $minPrice = $this->variants->min('price');
        $maxPrice = $this->variants->max('price');

        if ($minPrice == $maxPrice) {
            return number_format($minPrice, 0, ',', '.') . ' VNĐ';
        }
        return number_format($minPrice, 0, ',', '.') . ' - ' . number_format($maxPrice, 0, ',', '.') . ' VNĐ';
    }

    /**
     * Lấy URL đầy đủ của ảnh đại diện.
     * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        if ($this->mainImage) {
            return Storage::url($this->mainImage->image_path);
        }
        if ($this->firstImage) {
            return Storage::url($this->firstImage->image_path);
        }
        return asset('images/default-placeholder.png'); // Tạo ảnh mặc định tại public/images
    }

    /**
     * Tính tổng tồn kho từ tất cả các biến thể.
     * @return int
     */
    public function getTotalStockAttribute(): int
    {
        if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            return $this->variants->sum('stock');
        }
        return 0;
    }
}