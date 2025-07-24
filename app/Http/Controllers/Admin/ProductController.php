<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm.
     * Tải trước (eager load) các mối quan hệ cần thiết để tối ưu hóa truy vấn.
     */
    public function index()
    {
        $products = Product::with([
            'categories',
            'mainImage', // <-- Gọi mối quan hệ mainImage
            'firstImage', // <-- Gọi mối quan hệ firstImage
            'variants:id,product_id,price,stock'
        ])->latest()->paginate(10);

        return view('admins.products.index', compact('products'));
    }

    /**
     * Hiển thị form tạo sản phẩm mới.
     * Lấy dữ liệu cần thiết cho các ô select (danh mục, thuộc tính).
     */
    public function create()
    {
        $categories = Category::all();
        $attributes = Attribute::with('values')->get(); // Lấy thuộc tính và các giá trị của nó

        return view('admins.products.create', compact('categories', 'attributes'));
    }


// ...

        public function store(Request $request)
    {
        // === VALIDATION MỚI ===
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'images' => 'required|array|min:1', // Bắt buộc có ảnh chính
            'images.*' => 'image|max:2048',
            'variants' => 'required|array|min:1', // Bắt buộc phải tạo ít nhất 1 biến thể
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attribute_value_ids' => 'required|array',
            'variants.*.image' => 'nullable|image|max:2048', // Ảnh biến thể là tùy chọn
        ], [
            'images.required' => 'Bạn phải tải lên ít nhất một ảnh cho bộ sưu tập.',
            'variants.required' => 'Bạn phải tạo ít nhất một phiên bản sản phẩm.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Tạo sản phẩm chính
            $product = Product::create([
                'name' => $validated['name'],
                'code' => 'SP-' . strtoupper(Str::random(6)),
                'description' => $validated['description'] ?? null,
            ]);

            // 2. Gán danh mục
            if (!empty($validated['categories'])) {
                $product->categories()->sync($validated['categories']);
            }

            // 3. Lưu bộ sưu tập ảnh chính
            foreach ($request->file('images') as $key => $imageFile) {
                $path = $imageFile->store('products', 'public');
                $image = $product->images()->create([
                    'image_path' => $path,
                    'is_main' => ($key == 0), // Ảnh đầu tiên là ảnh chính
                ]);
                // Cập nhật cột image chính của sản phẩm
                if ($key == 0) {
                    $product->image = $path;
                    $product->save();
                }
            }

            // 4. Lưu các biến thể và ảnh của chúng
            foreach ($validated['variants'] as $key => $variantData) {
                $variant = $product->variants()->create([
                    'name'  => $variantData['name'],
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]);

                $variant->attributeValues()->sync($variantData['attribute_value_ids']);

                // === LƯU ẢNH RIÊNG CỦA BIẾN THỂ (NẾU CÓ) ===
                if ($request->hasFile("variants.{$key}.image")) {
                    $variantImageFile = $request->file("variants.{$key}.image");
                    $variantPath = $variantImageFile->store('products', 'public');
                    
                    // Lưu vào cùng bảng product_images, nhưng gán variant_id
                    ProductImage::create([
                        'product_id'         => $product->id,
                        'product_variant_id' => $variant->id,
                        'image_path'         => $variantPath,
                        'is_main'            => false,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi thêm sản phẩm: ' . $e->getMessage() . ' tại dòng ' . $e->getLine() . ' trong file ' . $e->getFile());
            return back()->with('error', 'Đã xảy ra lỗi không mong muốn. Vui lòng thử lại.')->withInput();
        }
    }

    /**
     * 
     */
    public function edit(Product $product)
    {
        // Tải trước tất cả dữ liệu liên quan một cách chính xác
        $product->load([
            'categories',
            'images', // Giờ sẽ chỉ tải ảnh của sản phẩm
            'variants.images', // Tải ảnh cho TỪNG biến thể
            'variants.attributeValues' // Tải thuộc tính cho TỪNG biến thể
        ]);

        $categories = Category::all();
        $attributes = Attribute::with('values')->get();

        return view('admins.products.edit', compact('product', 'categories', 'attributes'));
    }

    /**
     *
     */
    public function update(Request $request, Product $product)
    {
        // 1. VALIDATION
        $validated = $request->validate([
            // Rule 'unique' cần bỏ qua chính sản phẩm đang được sửa
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'images' => 'nullable|array', // Ảnh mới không bắt buộc khi update
            'images.*' => 'image|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attribute_value_ids' => 'required|array',
            'variants.*.image' => 'nullable|image|max:2048',
        ], [
            'variants.required' => 'Sản phẩm phải có ít nhất một phiên bản.',
        ]);

        DB::beginTransaction();
        try {
            // 2. CẬP NHẬT THÔNG TIN SẢN PHẨM CHÍNH
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            // 3. ĐỒNG BỘ DANH MỤC
            $product->categories()->sync($validated['categories'] ?? []);

            // 4. XỬ LÝ ẢNH
            // Xóa tất cả ảnh cũ (cả ảnh chính và ảnh biến thể)
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path); // Xóa file vật lý
                $image->delete(); // Xóa bản ghi trong DB
            }

            // Thêm lại bộ sưu tập ảnh chính mới
            $mainImagePath = null;
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_main' => ($key == 0),
                    ]);
                    if ($key == 0) {
                        $mainImagePath = $path;
                    }
                }
            }
            // Cập nhật lại cột 'image' của sản phẩm
            $product->image = $mainImagePath;
            $product->save();


            // 5. CẬP NHẬT BIẾN THỂ (XÓA CŨ - TẠO MỚI)
            $product->variants()->each(fn($variant) => $variant->delete()); // Xóa tất cả biến thể cũ

            foreach ($validated['variants'] as $key => $variantData) {
                $variant = $product->variants()->create([
                    'name'  => $variantData['name'],
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]);

                $variant->attributeValues()->sync($variantData['attribute_value_ids']);

                if ($request->hasFile("variants.{$key}.image")) {
                    $variantImageFile = $request->file("variants.{$key}.image");
                    $variantPath = $variantImageFile->store('products', 'public');
                    
                    ProductImage::create([
                        'product_id'         => $product->id,
                        'product_variant_id' => $variant->id,
                        'image_path'         => $variantPath,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật sản phẩm ' . $product->id . ': ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi cập nhật.')->withInput();
        }
    }

    public function show(Product $product)
    {
        // Tải trước các mối quan hệ cần thiết để tối ưu
        $product->load(['images', 'variants', 'categories']);

        // --- Logic lấy sản phẩm tương tự (giữ nguyên) ---
        $firstCategoryId = $product->categories->first()->id ?? null;
        $relatedProducts = collect();
        if ($firstCategoryId) {
            $relatedProducts = Product::whereHas('categories', function ($query) use ($firstCategoryId) {
                $query->where('category_id', $firstCategoryId);
            })
            ->where('id', '!=', $product->id)
            ->with(['thumbnail', 'variants'])
            ->inRandomOrder()
            ->take(4)
            ->get();
        }

        return view('clients.products.show', compact(
            'product', 
            'relatedProducts', 
            'cartCount'
        ));
    }

    /**
     * Xóa mềm sản phẩm.
     */
    public function destroy(Product $product)
    {
        $product->delete(); // Sử dụng SoftDeletes
        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm thành công.');
    }
}