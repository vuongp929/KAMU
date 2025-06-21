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
        // Tạo 1 tài khoản admin mẫu
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // hoặc bcrypt('password')
            'remember_token' => Str::random(10),
        ]);

        // Tạo thêm 5 user giả lập (nếu đã có UserFactory)
        User::factory(5)->create();
    }
}
}
