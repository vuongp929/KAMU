<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Category;
use Illuminate\Support\Facades\Cache; // Import Cache facade

class ClientLayoutComposer
{
    /**
     * Dữ liệu danh mục được lưu cache.
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $categories;

    /**
     * Khởi tạo composer, lấy dữ liệu danh mục từ cache nếu có.
     */
    public function __construct()
    {
        // Cache danh mục trong 60 phút để tránh truy vấn DB liên tục
        // 'categories_menu' là key của cache
        $this->categories = Cache::remember('categories_menu', 60 * 60, function () {
            return Category::whereNull('parent_id')
                           ->with('children') // Tải sẵn các danh mục con
                           ->get();
        });
    }

    /**
     * Gắn dữ liệu vào view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // --- LOGIC TÍNH TOÁN SỐ LƯỢNG GIỎ HÀNG ---
        // Giả sử giỏ hàng lưu trong session
        $cart = session()->get('cart', []);
        $cartCount = count($cart);

        // Gắn tất cả các biến cần thiết vào view
        $view->with([
            'categoriesForMenu' => $this->categories,
            'cartCount' => $cartCount,
        ]);
    }
}