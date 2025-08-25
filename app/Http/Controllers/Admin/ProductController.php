<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with([
            'categories',
            'mainImage',
            'firstImage',
            'variants:id,product_id,price,stock'
        ])->latest()->paginate(10);

        return view('admins.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $attributes = Attribute::with('values')->get();

        return view('admins.products.create', compact('categories', 'attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string|max:1000',
            'categories' => 'nullable|array|max:5',
            'categories.*' => 'exists:categories,id',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants' => 'required|array|min:1|max:20',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.name' => 'required|string|max:255|distinct',
            'variants.*.price' => 'required|numeric|min:0|max:999999999',
            'variants.*.stock' => 'required|integer|min:0|max:999999',
            'variants.*.attribute_value_ids' => 'required|array|min:1',
            'variants.*.attribute_value_ids.*' => 'exists:attribute_values,id',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'categories.max' => 'Chỉ được chọn tối đa 5 danh mục.',
            'categories.*.exists' => 'Danh mục được chọn không hợp lệ.',
            'images.required' => 'Bạn phải tải lên ít nhất một ảnh cho sản phẩm.',
            'images.max' => 'Chỉ được tải lên tối đa 10 ảnh.',
            'images.*.image' => 'File phải là ảnh.',
            'images.*.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            'variants.required' => 'Bạn phải tạo ít nhất một phiên bản sản phẩm.',
            'variants.max' => 'Chỉ được tạo tối đa 20 phiên bản.',
            'variants.*.name.required' => 'Tên phiên bản là bắt buộc.',
            'variants.*.name.distinct' => 'Tên các phiên bản phải khác nhau.',
            'variants.*.price.required' => 'Giá phiên bản là bắt buộc.',
            'variants.*.price.min' => 'Giá phải lớn hơn hoặc bằng 0.',
            'variants.*.price.max' => 'Giá không được vượt quá 999,999,999.',
            'variants.*.stock.required' => 'Số lượng tồn kho là bắt buộc.',
            'variants.*.stock.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0.',
            'variants.*.stock.max' => 'Số lượng tồn kho không được vượt quá 999,999.',
            'variants.*.attribute_value_ids.required' => 'Phải chọn ít nhất một thuộc tính cho phiên bản.',
            'variants.*.attribute_value_ids.*.exists' => 'Giá trị thuộc tính không hợp lệ.',
            'variants.*.image.image' => 'File phải là ảnh.',
            'variants.*.image.mimes' => 'Ảnh variant phải có định dạng: jpeg, png, jpg, gif, webp.',
            'variants.*.image.max' => 'Kích thước ảnh variant không được vượt quá 2MB.',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create([
                'name' => $validated['name'],
                'code' => 'SP-' . strtoupper(Str::random(6)),
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['categories'])) {
                $product->categories()->sync($validated['categories']);
            }

            // Lưu ảnh chính của sản phẩm (chỉ vào bảng product_images, không có product_variant_id)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_main' => ($key == 0),
                    ]);
                }
            }

            foreach ($validated['variants'] as $key => $variantData) {
                $variant = $product->variants()->create([
                    'name'  => $variantData['name'],
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]);

                $variant->attributeValues()->sync($variantData['attribute_value_ids']);

                // Lưu ảnh variant vào bảng product_variant_images thay vì product_images
                if ($request->hasFile("variants.{$key}.image")) {
                    $variantImageFile = $request->file("variants.{$key}.image");
                    $variantPath = $variantImageFile->store('products', 'public');

                    \App\Models\ProductVariantImage::create([
                        'product_variant_id' => $variant->id,
                        'path'               => $variantPath,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi thêm sản phẩm: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Đã xảy ra lỗi không mong muốn. Vui lòng thử lại.';
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage = 'Tên sản phẩm đã tồn tại. Vui lòng chọn tên khác.';
            } elseif (str_contains($e->getMessage(), 'storage')) {
                $errorMessage = 'Lỗi khi lưu ảnh. Vui lòng thử lại.';
            }
            
            return back()->with('error', $errorMessage)->withInput();
        }
    }

    public function edit(Product $product)
    {
        $product->load([
            'categories',
            'images',
            'variants.images',
            'variants.attributeValues'
        ]);

        $categories = Category::all();
        $attributes = Attribute::with('values')->get();

        return view('admins.products.edit', compact('product', 'categories', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attribute_value_ids' => 'required|array',
            'variants.*.attribute_value_ids.*' => 'exists:attribute_values,id',
        ], [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'categories.max' => 'Chỉ được chọn tối đa 5 danh mục.',
            'categories.*.exists' => 'Danh mục được chọn không hợp lệ.',
            'images.max' => 'Chỉ được tải lên tối đa 10 ảnh.',
            'images.*.image' => 'File phải là ảnh.',
            'images.*.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            'variants.required' => 'Sản phẩm phải có ít nhất một phiên bản.',
            'variants.max' => 'Chỉ được tạo tối đa 20 phiên bản.',
            'variants.*.name.required' => 'Tên phiên bản là bắt buộc.',
            'variants.*.name.distinct' => 'Tên các phiên bản phải khác nhau.',
            'variants.*.price.required' => 'Giá phiên bản là bắt buộc.',
            'variants.*.price.min' => 'Giá phải lớn hơn hoặc bằng 0.',
            'variants.*.price.max' => 'Giá không được vượt quá 999,999,999.',
            'variants.*.stock.required' => 'Số lượng tồn kho là bắt buộc.',
            'variants.*.stock.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0.',
            'variants.*.stock.max' => 'Số lượng tồn kho không được vượt quá 999,999.',
            'variants.*.attribute_value_ids.required' => 'Phải chọn ít nhất một thuộc tính cho phiên bản.',
            'variants.*.attribute_value_ids.*.exists' => 'Giá trị thuộc tính không hợp lệ.',
            'variants.*.image.image' => 'File phải là ảnh.',
            'variants.*.image.mimes' => 'Ảnh variant phải có định dạng: jpeg, png, jpg, gif, webp.',
            'variants.*.image.max' => 'Kích thước ảnh variant không được vượt quá 2MB.',
        ]);

        DB::beginTransaction();
        try {
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $product->categories()->sync($validated['categories'] ?? []);

            // Chỉ xóa và cập nhật ảnh khi có ảnh mới được upload
            if ($request->hasFile('images')) {
                // Xóa ảnh cũ
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }

                // Thêm ảnh mới
                foreach ($request->file('images') as $key => $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_main' => ($key == 0),
                    ]);
                }
            }

            // Tối ưu: Chỉ cập nhật variants thay đổi thay vì xóa tất cả
            $existingVariants = $product->variants()->with(['images', 'attributeValues'])->get()->keyBy('id');
            $submittedVariantIds = collect($validated['variants'])->pluck('id')->filter();
            
            // Xóa variants không còn tồn tại (không có trong form submit)
            foreach ($existingVariants as $existingVariant) {
                if (!$submittedVariantIds->contains($existingVariant->id)) {
                    // Xóa ảnh của variant từ bảng product_variant_images
                    foreach ($existingVariant->images as $image) {
                        Storage::disk('public')->delete($image->path);
                        $image->delete();
                    }
                    $existingVariant->delete();
                }
            }

            foreach ($validated['variants'] as $key => $variantData) {
                $variantId = $variantData['id'] ?? null;
                
                if ($variantId && isset($existingVariants[$variantId])) {
                    // Cập nhật variant hiện có
                    $variant = $existingVariants[$variantId];
                    
                    // Chỉ cập nhật nếu có thay đổi
                    $needsUpdate = $variant->name !== $variantData['name'] ||
                                   $variant->price != $variantData['price'] ||
                                   $variant->stock != $variantData['stock'];
                    
                    if ($needsUpdate) {
                        $variant->update([
                            'name'  => $variantData['name'],
                            'price' => $variantData['price'],
                            'stock' => $variantData['stock'],
                        ]);
                    }
                } else {
                    // Tạo variant mới
                    $variant = $product->variants()->create([
                        'name'  => $variantData['name'],
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ]);
                }

                // Chỉ sync attribute values nếu có thay đổi
                $currentAttributeIds = $variant->attributeValues->pluck('id')->sort()->values();
                $newAttributeIds = collect($variantData['attribute_value_ids'])->sort()->values();
                
                if ($currentAttributeIds->toArray() !== $newAttributeIds->toArray()) {
                    $variant->attributeValues()->sync($variantData['attribute_value_ids']);
                }

                // Xử lý ảnh variant nếu có upload mới
                if ($request->hasFile("variants.{$key}.image")) {
                    // Xóa ảnh cũ của variant này
                    foreach ($variant->images as $image) {
                        Storage::disk('public')->delete($image->path);
                        $image->delete();
                    }
                    
                    // Thêm ảnh mới vào bảng product_variant_images
                    $variantImageFile = $request->file("variants.{$key}.image");
                    $variantPath = $variantImageFile->store('products', 'public');

                    \App\Models\ProductVariantImage::create([
                        'product_variant_id' => $variant->id,
                        'path'               => $variantPath,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật sản phẩm ' . $product->id . ': ' . $e->getMessage(), [
                'product_id' => $product->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Đã xảy ra lỗi khi cập nhật.';
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $errorMessage = 'Tên sản phẩm đã tồn tại. Vui lòng chọn tên khác.';
            } elseif (str_contains($e->getMessage(), 'storage')) {
                $errorMessage = 'Lỗi khi lưu ảnh. Vui lòng thử lại.';
            } elseif (str_contains($e->getMessage(), 'foreign key')) {
                $errorMessage = 'Không thể cập nhật do ràng buộc dữ liệu. Vui lòng kiểm tra lại.';
            }
            
            return back()->with('error', $errorMessage)->withInput();
        }
    }

    public function show(Product $product)
    {
        // Kiểm tra nếu request muốn JSON (cho modal)
        if (request()->wantsJson() || request()->ajax()) {
            $product->load(['images', 'variants', 'categories']);
            return response()->json($product);
        }

        // Trả về view cho trang chi tiết thông thường
        $product->load(['images', 'variants', 'categories']);

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
            'relatedProducts'
        ));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back();
    }
}
