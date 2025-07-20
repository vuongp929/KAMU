<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị trang chi tiết sản phẩm.
     */
    public function show(Product $product)
    {
        // Tải trước (load) các mối quan hệ cần thiết cho sản phẩm chính đang xem
        $product->load([
            'images',
            'variants.attributeValues',
            'categories',
            'mainImage', // Tải ảnh chính
            'firstImage' // Tải ảnh đầu tiên (phòng trường hợp không có ảnh chính)
        ]);

        // --- Logic lấy sản phẩm tương tự ---
        $firstCategoryId = $product->categories->first()->id ?? null;
        $relatedProducts = collect(); // Tạo một collection rỗng

        if ($firstCategoryId) {
            $relatedProducts = Product::whereHas('categories', function ($query) use ($firstCategoryId) {
                $query->where('category_id', $firstCategoryId);
            })
            ->where('id', '!=', $product->id) // Không lấy chính nó

            // === BẮT ĐẦU SỬA LỖI TẠI ĐÂY ===
            // Thay thế 'thumbnail' bằng 'mainImage' và 'firstImage' cho các sản phẩm liên quan
            ->with(['mainImage', 'firstImage', 'variants'])
            // === KẾT THÚC SỬA LỖI ===

            ->inRandomOrder() // Lấy ngẫu nhiên
            ->take(4) // Giới hạn 4 sản phẩm
            ->get();
        }

        return view('clients.products.show', compact('product', 'relatedProducts'));
    }
}