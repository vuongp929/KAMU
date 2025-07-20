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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade');

            // ✅ Ràng buộc tới biến thể sản phẩm (có giá riêng, size riêng)
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');

            $table->unsignedInteger('quantity');
            $table->string('size')->nullable(); // nếu size nằm riêng
            $table->decimal('price_at_order', 12, 2)->nullable(); // nên thêm để lưu giá tĩnh tại thời điểm thêm vào giỏ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
