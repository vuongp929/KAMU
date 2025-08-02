<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_discount_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('discount_code')->unique();
            $table->decimal('discount_percentage', 5, 4); // Lưu phần trăm giảm giá (0.0001 = 0.01%)
            $table->integer('points_used'); // Số điểm đã sử dụng để quy đổi
            $table->dateTime('expires_at'); // Thời hạn sử dụng
            $table->boolean('is_used')->default(false); // Đã sử dụng chưa
            $table->dateTime('used_at')->nullable(); // Thời gian sử dụng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_discount_codes');
    }
}; 