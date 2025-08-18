<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id']; // Hoặc các trường khác nếu có

    /**
     * A cart has many items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    
    // ==========================================================
    // === BẮT ĐẦU PHẦN ACCESSOR TÍNH TOÁN ======================
    // ==========================================================

    /**
     * Accessor để tính tổng tiền của giỏ hàng một cách linh động.
     * Laravel sẽ tự động gọi hàm này khi bạn truy cập $cart->total_price trong Blade.
     *
     * @return float
     */
    public function getTotalPriceAttribute(): float
    {
        // 'reduce' là một cách an toàn để tính tổng trên một collection.
        // Nó lặp qua từng $item trong $this->items (các sản phẩm trong giỏ).
        return $this->items->reduce(function ($total, $item) {
            // $total là tổng tích lũy, $item là sản phẩm hiện tại trong vòng lặp.
            
            // Lấy giá từ CartItem hoặc từ ProductVariant
            $price = $item->price ?? ($item->variant ? $item->variant->price : 0);
            
            // Cộng dồn vào tổng: (tổng cũ + số lượng * giá)
            return $total + ($item->quantity * $price);
        }, 0); // 0 là giá trị khởi tạo của $total.
    }

    /**
     * Accessor để tính tổng số lượng sản phẩm trong giỏ hàng.
     *
     * @return int
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }
    
    // ==========================================================
    // === KẾT THÚC PHẦN ACCESSOR TÍNH TOÁN =======================
    // ==========================================================
}
