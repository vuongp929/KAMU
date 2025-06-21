<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function canApplyToOrder($orderAmount)
    {
        return $orderAmount >= $this->min_order_amount;
    }

    public function incrementUsageCount()
    {
        $this->increment('used_count');
    }
}
