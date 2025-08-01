<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       User::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',

        ]
    );

        // Tạo thêm 5 user giả lập (nếu đã có UserFactory)
        User::factory(5)->create();
    }
}
