<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'payment_status',
        'total_price',
        'shipping_address',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    // ✅ Định nghĩa quan hệ
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
