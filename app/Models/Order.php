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
}
