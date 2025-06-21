<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->tinyInteger('stars')->nullable()->unsigned(); // nếu có đánh giá sao
            $table->text('content')->nullable();                  // nội dung đánh giá/bình luận
            $table->foreignId('parent_id')->nullable()->constrained('product_reviews')->onDelete('cascade'); // reply
            $table->boolean('is_hidden')->default(false); // ẩn/hiện bình luận
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_reviews');
    }
};

