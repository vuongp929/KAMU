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
        Schema::table('orders', function (Blueprint $table) {
            // Thêm cột để lưu mã code đã sử dụng
            $table->string('discount_code')->nullable()->after('payment_status');
            // (Tùy chọn) Thêm cột để lưu số tiền đã giảm
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
