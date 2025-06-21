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
                'discount' => 10,
                'start_at' => now(),
                'end_at' => now()->addMonths(1),
                'min_order_amount' => 100000,
                'max_uses' => 100,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER2024',
                'discount' => 20,
                'start_at' => now()->addMonth(),
                'end_at' => now()->addMonths(3),
                'min_order_amount' => 200000,
                'max_uses' => 50,
                'used_count' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'SPECIAL50',
                'discount' => 50,
                'start_at' => now(),
                'end_at' => now()->addDays(7),
                'min_order_amount' => 500000,
                'max_uses' => 10,
                'used_count' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($discounts as $discount) {
            Discount::create($discount);
        }
    }
}
