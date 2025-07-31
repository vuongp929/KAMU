<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Discount extends Model
{
    use HasFactory;
    protected $table = 'discounts';

    protected $fillable = [
        'code',
        'discount',
        'max_uses',
        'used_count',
        'start_at',
        'end_at',
        'min_order_amount',
        'applicable_user_ids',
        'applicable_category_ids',
        'is_active',
        'discount_type',
        'amount',
        'once_per_order',
    ];

    protected $dates = [
        'start_at',
        'end_at',
    ];

    public function isValid()
    {
        $now = now();
        return $this->is_active &&
               ($this->max_uses === null || $this->used_count < $this->max_uses) &&
               $now->between($this->start_at, $this->end_at);
    }

    public function getStatus()
    {
        $now = now();
        
        if (!$this->is_active) {
            return 'disabled';
        }
        
        // Đảm bảo so sánh datetime chính xác
        try {
            $startAt = $this->start_at ? \Carbon\Carbon::parse($this->start_at) : null;
            $endAt = $this->end_at ? \Carbon\Carbon::parse($this->end_at) : null;
            
            if ($startAt && $now < $startAt) {
                return 'not_started';
            }
            
            if ($endAt && $now > $endAt) {
                return 'expired';
            }
            
            if ($this->max_uses <= 0) {
                return 'used_up';
            }
            
            return 'active';
        } catch (\Exception $e) {
            // Nếu có lỗi parse datetime, trả về active để tránh lỗi
            return 'active';
        }
    }

    public function getRemainingUses()
    {
        if ($this->max_uses === null) {
            return null; // Không giới hạn
        }
        return max(0, $this->max_uses);
    }

    public function canApplyToOrder($orderAmount)
    {
        return $orderAmount >= $this->min_order_amount;
    }

    public function incrementUsageCount()
    {
        $this->increment('used_count');
        // Giảm số lượng tối đa khi sử dụng
        if ($this->max_uses > 0) {
            $this->decrement('max_uses');
        }
    }

    /**
     * Lấy giá trị giảm giá thực tế dựa vào loại giảm giá
     */
    public function getDiscountValue($orderAmount)
    {
        if ($this->discount_type === 'amount') {
            return min($this->amount, $orderAmount); // Không vượt quá tổng đơn
        }
        // Mặc định là percent
        return $orderAmount * $this->discount / 100;
    }
}
