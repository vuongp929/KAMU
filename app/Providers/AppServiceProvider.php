<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ==========================================================
        // === BẮT ĐẦU PHẦN CODE VIEW COMPOSER ======================
        // ==========================================================

        // Đăng ký View Composer cho header của client
        View::composer('clients.layouts.header', function ($view) {
            
            // --- LOGIC TÍNH TOÁN SỐ LƯỢNG GIỎ HÀNG ---
            // Giả sử bạn lưu giỏ hàng trong session với key là 'cart'
            $cart = session()->get('cart', []);
            $cartCount = count($cart);

            $categories = Category::whereNull('parent_id')
                                ->with('children') // Giả sử bạn có mối quan hệ 'children' trong Model Category
                                ->get();
            
            // Gắn biến $cartCount vào view
            $view->with([
                'cartCount' => $cartCount,
                'categories' => $categories, // <-- Thêm biến mới vào đây
            ]);
        });

        // ==========================================================
        // === KẾT THÚC PHẦN CODE VIEW COMPOSER =======================
        // ==========================================================
    }
}