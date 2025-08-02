<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            [
                'code' => 'WELCOME2024',
                'discount_type' => 'percent',
                'discount' => 10,
                'amount' => null,
                'once_per_order' => false,
                'start_at' => now(),
                'end_at' => now()->addMonths(1),
                'min_order_amount' => 100000,
                'max_uses' => 100,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER2024',
                'discount_type' => 'percent',
                'discount' => 20,
                'amount' => null,
                'once_per_order' => false,
                'start_at' => now()->addMonth(),
                'end_at' => now()->addMonths(3),
                'min_order_amount' => 200000,
                'max_uses' => 50,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'SPECIAL50',
                'discount_type' => 'percent',
                'discount' => 50,
                'amount' => null,
                'once_per_order' => false,
                'start_at' => now(),
                'end_at' => now()->addDays(7),
                'min_order_amount' => 500000,
                'max_uses' => 10,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'MONEY100K',
                'discount_type' => 'amount',
                'discount' => null,
                'amount' => 100000,
                'once_per_order' => true,
                'start_at' => now(),
                'end_at' => now()->addDays(30),
                'min_order_amount' => 300000,
                'max_uses' => 20,
                'used_count' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($discounts as $discount) {
            Discount::create($discount);
        }
    }
}
