<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        // === BẮT ĐẦU PHẦN SỬA LẠI ===

        // 1. Dữ liệu cho Header đã được tự động truyền qua ViewComposer
        // Không cần lấy $categoriesForMenu và $cartCount nữa

        // 2. Lấy dữ liệu riêng cho trang chủ
        $newProducts = Product::with(['mainImage', 'firstImage', 'variants'])
                          ->latest()
                          ->take(8)
                          ->get();

    $featuredProducts = Product::with(['mainImage', 'firstImage', 'variants'])
                               ->inRandomOrder()
                               ->take(4)
                               ->get();
        // 3. Trả về view với dữ liệu riêng cho trang chủ
        // $categoriesForMenu và $cartCount đã được ViewComposer tự động truyền
        return view('clients.home', [
            'newProducts' => $newProducts,
            'featuredProducts' => $featuredProducts,
        ]);

    }

    // Khi bạn tạo các phương thức khác, ví dụ trang chi tiết sản phẩm,
    // bạn cũng sẽ cần lấy và truyền '$categoriesForMenu' và '$cartCount' tương tự.
    public function showProduct($slug) 
    {
        // $categoriesForMenu = ... (lấy dữ liệu header)
        // $cartCount = ... (lấy dữ liệu header)
        // $product = Product::where('slug', $slug)->firstOrFail();

        // return view('clients.product-detail', compact(
        //     'categoriesForMenu', 
        //     'cartCount',
        //     'product'
        // ));
    }
}