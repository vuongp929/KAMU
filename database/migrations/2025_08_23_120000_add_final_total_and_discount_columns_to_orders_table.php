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
            $table->decimal('final_total', 10, 2)->nullable()->after('total_price');
            $table->string('discount_code')->nullable()->after('final_total');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('discount_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['final_total', 'discount_code', 'discount_amount']);
        });
    }
};