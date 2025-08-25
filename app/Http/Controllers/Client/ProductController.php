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
     * Hiển thị danh sách sản phẩm với chức năng lọc và sắp xếp.
     */
    public function index(Request $request)
    {
        $query = Product::with(['mainImage', 'firstImage', 'variants', 'categories']);
        $currentCategory = null;
        
        // Lọc theo từ khóa tìm kiếm
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Lọc theo danh mục (hỗ trợ cả ID và slug)
        if ($request->filled('category')) {
            $categoryParam = $request->category;
            
            // Tìm danh mục hiện tại để hiển thị thông tin
            if (is_numeric($categoryParam)) {
                $currentCategory = Category::find($categoryParam);
            } else {
                $currentCategory = Category::where('slug', $categoryParam)->first();
            }
            
            $query->whereHas('categories', function($q) use ($categoryParam) {
                // Kiểm tra nếu là số (ID) hay chuỗi (slug)
                if (is_numeric($categoryParam)) {
                    $q->where('categories.id', $categoryParam);
                } else {
                    $q->where('categories.slug', $categoryParam);
                }
            });
        }
        
        // Lọc theo khoảng giá
        if ($request->filled('min_price')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }
        
        if ($request->filled('max_price')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }
        
        // Sắp xếp
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'price_asc':
                $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                      ->select('products.*')
                      ->groupBy('products.id')
                      ->orderBy(\DB::raw('MIN(product_variants.price)'), 'asc');
                break;
            case 'price_desc':
                $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')
                      ->select('products.*')
                      ->groupBy('products.id')
                      ->orderBy(\DB::raw('MIN(product_variants.price)'), 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popular':
                // Sắp xếp theo số lượng đã bán (giả sử có trường sold_count)
                $query->orderBy('created_at', 'desc'); // Tạm thời dùng created_at
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $products = $query->paginate(12)->appends($request->query());
        
        // Lấy danh mục cho filter
        $categories = Category::where('statu', 1)->get();
        
        return view('clients.products.index', compact('products', 'categories', 'currentCategory'));
    }
    
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