<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'status',
        'payment_status',
        'payment_method',
        'total_price',
        'shipping_address',
    ];

    // Quan hệ với khách hàng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với các chi tiết đơn hàng
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id'); // user_id là khóa ngoại trong bảng orders
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Lấy thông tin shipping từ JSON
     */
    public function getShippingInfoAttribute()
    {
        if ($this->shipping_address) {
            return json_decode($this->shipping_address, true);
        }
        return null;
    }

    /**
     * Lấy tên khách hàng
     */
    public function getCustomerNameAttribute()
    {
        $shippingInfo = $this->shipping_info;
        return $shippingInfo['name'] ?? 'N/A';
    }

    /**
     * Lấy email khách hàng
     */
    public function getCustomerEmailAttribute()
    {
        $shippingInfo = $this->shipping_info;
        return $shippingInfo['email'] ?? 'N/A';
    }

    /**
     * Lấy số điện thoại khách hàng
     */
    public function getCustomerPhoneAttribute()
    {
        $shippingInfo = $this->shipping_info;
        return $shippingInfo['phone'] ?? 'N/A';
    }

    /**
     * Lấy địa chỉ khách hàng
     */
    public function getCustomerAddressAttribute()
    {
        $shippingInfo = $this->shipping_info;
        return $shippingInfo['address'] ?? 'N/A';
    }

    /**
     * Cộng điểm thưởng khi thanh toán thành công
     */
    public function addRewardPointsOnPaymentSuccess()
    {
        if ($this->payment_status === 'paid' && $this->user) {
            $this->user->reward_points += 20;
            $this->user->save();
            return true;
        }
        return false;
    }

    /**
     * Kiểm tra xem đơn hàng đã được cộng điểm chưa
     */
    public function hasReceivedRewardPoints()
    {
        // Có thể thêm một cột để track việc đã cộng điểm hay chưa
        // Hoặc dựa vào payment_status
        return $this->payment_status === 'paid';
    }
}
