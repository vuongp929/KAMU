<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\ProductReview;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Discount;
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

        $canReview = false;
        if (Auth::check()) {
            $canReview = \App\Models\Order::where('user_id', Auth::id())
                ->whereIn('status', ['completed', 'delivered'])
                ->whereHas('orderItems', function($q) use ($product) {
                    $q->where('product_id', $product->id);
                })->exists();
        }
        return view('clients.products.show', compact('product', 'relatedProducts', 'canReview'));
    }
    //     public function search(Request $request)
    // {
    //     $query = $request->input('query');

    //     $products = Product::with('variants')
    //         ->where(function ($q) use ($query) {
    //             $q->where('name', 'LIKE', "%$query%")
    //             ->orWhere('description', 'LIKE', "%$query%");
    //         })
    //         ->paginate(6); // hoặc bao nhiêu sản phẩm/trang tùy ông


    //     return view('clients.search_results', compact('products', 'query'));
    // }
        public function search(Request $request)
    {
        $keyword = $request->input('query');
        
        // 1. Tìm sản phẩm theo tên hoặc mô tả
        $productsByName = Product::with('variants')
            ->where('name', 'LIKE', "%$keyword%")
            ->orWhere('description', 'LIKE', "%$keyword%")
            ->get();

        // 2. Tìm theo danh mục
        $categories = Category::where('name', 'LIKE', "%$keyword%")->pluck('id');
        $productsByCategory = Product::with('variants')
            ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categories))
            ->get();

        // 3. Tìm theo size/price trong variants
        $productsByVariant = Product::with('variants')
            ->whereHas('variants', fn($q) =>
                $q->where('size', 'LIKE', "%$keyword%")
                ->orWhere('price', 'LIKE', "%$keyword%")
            )
            ->get();

        // 4. Tìm theo đánh giá nội dung
        $productsByReview = Product::with('variants')
            ->whereHas('reviews', fn($q) =>
                $q->where('content', 'LIKE', "%$keyword%")
            )
            ->get();

        // 5. Tìm theo thuộc tính (màu sắc, kiểu dáng,...)
        $attributeValues = AttributeValue::where('value', 'LIKE', "%$keyword%")->pluck('id');
        $productsByAttribute = Product::with('variants')
            ->whereHas('variants.attributeValues', fn($q) =>
                $q->whereIn('attribute_values.id', $attributeValues)
            )
            ->get();

        // 6. Gợi ý: Tìm sản phẩm theo mã giảm giá nếu muốn
        // -> có thể bỏ qua nếu không muốn
        $discounts = Discount::where('code', 'LIKE', "%$keyword%")->get(); // optional

        // 7. Gộp kết quả và loại bỏ trùng
        $allProducts = $productsByName
            ->merge($productsByCategory)
            ->merge($productsByVariant)
            ->merge($productsByReview)
            ->merge($productsByAttribute)
            ->unique('id');

        // 8. Phân trang thủ công (nếu cần)
        $perPage = 8;
        $currentPage = request()->get('page', 1);
        $pagedResults = new \Illuminate\Pagination\LengthAwarePaginator(
            $allProducts->forPage($currentPage, $perPage),
            $allProducts->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('clients.search_results', [
            'products' => $pagedResults,
            'query' => $keyword,
        ]);
    }

}