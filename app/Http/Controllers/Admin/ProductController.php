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
            return redirect()->route('admins.products.index')->with('success', 'Thêm sản phẩm thành công!');

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
        // Sửa lại rule 'unique' để nó bỏ qua chính sản phẩm đang được sửa
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'images' => 'nullable|array', // Ảnh mới không bắt buộc khi update
            'images.*' => 'image|max:2048',
            'price' => 'required_without:variants|nullable|numeric|min:0',
            'stock' => 'required_without:variants|nullable|integer|min:0',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required_with:variants|string|max:255',
            'variants.*.price' => 'required_with:variants|numeric|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'variants.*.attribute_value_ids' => 'nullable|array',
            'variants.*.images' => 'nullable|array',
            'variants.*.images.*' => 'image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // 2. CẬP NHẬT THÔNG TIN SẢN PHẨM CHÍNH
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            // 3. CẬP NHẬT DANH MỤC
            if ($request->has('categories')) {
                $product->categories()->sync($validated['categories']);
            } else {
                // Nếu không có category nào được chọn, hãy xóa tất cả các liên kết cũ
                $product->categories()->detach();
            }

            // 4. THÊM ẢNH MỚI (nếu có)
            if ($request->hasFile('images')) {
                $isFirstNewImage = !$product->images()->where('is_main', true)->exists();
                foreach ($request->file('images') as $key => $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $isMain = $isFirstNewImage && ($key == 0); // Đánh dấu ảnh chính nếu chưa có

                    $image = $product->images()->create([
                        'image_path' => $path,
                        'is_main' => $isMain,
                    ]);

                    // Cập nhật lại cột 'image' của sản phẩm nếu vừa thêm ảnh chính mới
                    if ($isMain) {
                        $product->image = $path;
                        $product->save();
                    }
                }
            }
            // Lưu ý: Logic xóa ảnh cũ cần một cơ chế riêng (ví dụ: nút xóa cho từng ảnh)
            // và không nên thực hiện ở đây để tránh phức tạp.

            // 5. CẬP NHẬT BIẾN THỂ (THEO LOGIC XÓA CŨ - TẠO MỚI)
            // Xóa tất cả các biến thể cũ và các mối quan hệ của chúng (ảnh, thuộc tính)
            $product->variants()->each(function ($variant) {
                $variant->delete(); // Sử dụng delete của model để kích hoạt các event (nếu có)
            });

            if (!empty($validated['variants'])) {
                // Tái sử dụng logic từ hàm store để tạo lại các biến thể
                foreach ($request->variants as $key => $variantData) {
                    $variant = $product->variants()->create([
                        'name'  => $variantData['name'],
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ]);

                    if (!empty($variantData['attribute_value_ids'])) {
                        $variant->attributeValues()->sync($variantData['attribute_value_ids']);
                    }

                    if ($request->hasFile("variants.{$key}.images")) {
                        foreach ($request->file("variants.{$key}.images") as $variantImageFile) {
                            $variantPath = $variantImageFile->store('products', 'public');
                            ProductImage::create([
                                'product_id'         => $product->id,
                                'product_variant_id' => $variant->id,
                                'image_path'         => $variantPath,
                                'is_main'            => false,
                            ]);
                        }
                    }
                }
            } else {
                // Nếu không có biến thể nào được gửi lên, tạo một biến thể mặc định
                $product->variants()->create([
                    'name'  => $product->name,
                    'price' => $validated['price'] ?? 0,
                    'stock' => $validated['stock'] ?? 0,
                ]);
            }

            DB::commit();
            return redirect()->route('admins.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật sản phẩm ' . $product->id . ': ' . $e->getMessage() . ' tại dòng ' . $e->getLine() . ' trong file ' . $e->getFile());
            return back()->with('error', 'Đã xảy ra lỗi không mong muốn khi cập nhật.')->withInput();
        }
    }

    public function show(Product $product)
    {
        // Tải tất cả các mối quan hệ cần thiết để hiển thị chi tiết
        $product->load([
            'categories:name', // Chỉ lấy tên danh mục
            'images',          // Lấy ảnh chính
            'variants.attributeValues.attribute', // Lấy thuộc tính và giá trị của biến thể
            'variants.images'  // Lấy ảnh của biến thể
        ]);

        // Trả về dữ liệu dưới dạng JSON
        return response()->json($product);
    }

    /**
     * Xóa mềm sản phẩm.
     */
    public function destroy(Product $product)
    {
        $product->delete(); // Sử dụng SoftDeletes
        return redirect()->route('admins.products.index')->with('success', 'Đã xóa sản phẩm thành công.');
    }
}