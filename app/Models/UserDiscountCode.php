<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'discount_code',
        'discount_percentage',
        'points_used',
        'expires_at',
        'is_used',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Quan hệ với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra xem mã có còn hiệu lực không
     */
    public function isExpired()
    {
        return now()->isAfter($this->expires_at);
    }

    /**
     * Kiểm tra xem mã có thể sử dụng không
     */
    public function canBeUsed()
    {
        return !$this->is_used && !$this->isExpired();
    }

    /**
     * Đánh dấu mã đã được sử dụng
     */
    public function markAsUsed()
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Lấy trạng thái hiển thị
     */
    public function getStatusTextAttribute()
    {
        if ($this->is_used) {
            return 'Đã sử dụng';
        }
        
        if ($this->isExpired()) {
            return 'Hết hạn';
        }
        
        return 'Có thể sử dụng';
    }

    /**
     * Lấy màu sắc cho trạng thái
     */
    public function getStatusColorAttribute()
    {
        if ($this->is_used) {
            return 'secondary';
        }
        
        if ($this->isExpired()) {
            return 'danger';
        }
        
        return 'success';
    }
} 