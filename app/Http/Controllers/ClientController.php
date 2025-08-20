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

        // 1. Lấy dữ liệu cho Header (sẽ được dùng trên nhiều trang)
        // $categoriesForMenu = Category::whereNull('parent_id')
        //                      ->where('statu', 1)
        //                      ->with('children') // children đã tự lọc sẵn
        //                      ->get();

       $categoriesForMenu = Category::whereNull('parent_id')
        ->where('statu', 1)
        ->with('activeChildren') 
        ->get();


        $cartCount = count(session('cart', []));

        // 2. Lấy dữ liệu riêng cho trang chủ
        $newProducts = Product::with(['mainImage', 'firstImage', 'variants'])
                          ->latest()
                          ->take(8)
                          ->get();

    $featuredProducts = Product::with(['mainImage', 'firstImage', 'variants'])
                               ->inRandomOrder()
                               ->take(4)
                               ->get();
        // 3. Trả về view với TẤT CẢ dữ liệu
        return view('clients.home', [
            // 'categories' => $categoriesForMenu, 
            'categoriesForMenu' => $categoriesForMenu, 
            'cartCount' => $cartCount,
            'newProducts' => $newProducts,
            'featuredProducts' => $featuredProducts,
            // Giờ đây 'categories' đã được truyền thẳng vào view 'home'
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