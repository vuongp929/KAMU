<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Cart;

class ClientLayoutComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // 1. Lấy tất cả danh mục cha và các con của chúng cho menu
        $categoriesForMenu = Category::whereNull('parent_id')
                                     ->with('children') // Tải trước các danh mục con để tối ưu
                                     ->get();

        // 2. Lấy số lượng sản phẩm trong giỏ hàng
        $cartCount = 0;
        if (Auth::check()) { // Chỉ kiểm tra khi người dùng đã đăng nhập
            $cart = Cart::where('user_id', Auth::id())->withCount('items')->first();
            if ($cart) {
                // withCount('items') sẽ tạo ra một thuộc tính 'items_count'
                $cartCount = $cart->items_count;
            }
        }

        // 3. Gắn các biến này vào view
        $view->with('categoriesForMenu', $categoriesForMenu);
        $view->with('cartCount', $cartCount);
    }
}