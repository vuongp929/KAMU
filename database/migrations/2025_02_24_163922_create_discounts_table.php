<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('discount')->unsigned()->nullable();
            
            $table->dateTime('start_at')->nullable();   
            $table->dateTime('end_at')->nullable();  
            
            $table->decimal('min_order_amount', 10, 2)->default(0); 
            
            $table->integer('max_uses')->unsigned()->nullable();
            $table->integer('used_count')->unsigned()->default(0); 
            
            $table->json('applicable_user_ids')->nullable(); 
            $table->json('applicable_category_ids')->nullable();
            
            $table->boolean('is_active')->default(true);
            
            $table->string('discount_type')->default('percent');
            $table->decimal('amount', 15, 2)->nullable();
            $table->boolean('once_per_order')->default(false);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discounts');
    }
};
