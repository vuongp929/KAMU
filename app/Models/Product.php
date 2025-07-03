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

    protected $table = 'products';

    protected $fillable = [
        'code',
        'name',
        'description',
        'image',
    ];

    //======================================================================
    // MỐI QUAN HỆ (RELATIONSHIPS)
    //======================================================================

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        // Thêm điều kiện whereNull để chỉ lấy các ảnh có product_variant_id là NULL
        return $this->hasMany(ProductImage::class)->whereNull('product_variant_id');
    }

    /**
     * === BẮT ĐẦU PHẦN SỬA LỖI QUAN TRỌNG ===
     * Định nghĩa mối quan hệ để lấy ảnh được đánh dấu là ảnh chính.
     * Mối quan hệ này rất đơn giản và hoạt động tốt với with().
     */
    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    /**
     * Định nghĩa mối quan hệ để lấy ảnh đầu tiên (cũ nhất).
     * Mối quan hệ này cũng rất đơn giản và hoạt động tốt với with().
     */
    public function firstImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->oldestOfMany();
    }
    // === KẾT THÚC PHẦN SỬA LỖI QUAN TRỌNG ===

    //======================================================================
    // THUỘC TÍNH ẢO (ACCESSORS)
    //======================================================================

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
     * Accessor 'thumbnail_url' giờ sẽ sử dụng các mối quan hệ đã được tách biệt.
     */
    public function getThumbnailUrlAttribute(): string
    {
        // Ưu tiên 1: Lấy từ cột 'image' đã lưu sẵn để có tốc độ nhanh nhất.
        if ($this->image) {
            return Storage::url($this->image);
        }

        // Ưu tiên 2: Tìm ảnh chính thông qua mối quan hệ 'mainImage'.
        // $this->mainImage sẽ gọi đến phương thức mainImage() và trả về kết quả.
        if ($this->mainImage) {
            return Storage::url($this->mainImage->image_path);
        }

        // Ưu tiên 3: Nếu không có ảnh chính, lấy ảnh đầu tiên qua 'firstImage'.
        if ($this->firstImage) {
            return Storage::url($this->firstImage->image_path);
        }

        // Trường hợp cuối cùng: Trả về ảnh mặc định.
        return asset('images/default-placeholder.png');
    }

    public function getTotalStockAttribute(): int
    {
        if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            return $this->variants->sum('stock');
        }
        return 0;
    }
}