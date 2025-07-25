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
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attribute_value_ids' => 'required|array',
            'variants.*.image' => 'nullable|image|max:2048',
        ], [
            'images.required' => 'Bạn phải tải lên ít nhất một ảnh cho bộ sưu tập.',
            'variants.required' => 'Bạn phải tạo ít nhất một phiên bản sản phẩm.',
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

            foreach ($request->file('images') as $key => $imageFile) {
                $path = $imageFile->store('products', 'public');
                $image = $product->images()->create([
                    'image_path' => $path,
                    'is_main' => ($key == 0),
                ]);
                if ($key == 0) {
                    $product->image = $path;
                    $product->save();
                }
            }

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
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'images' => 'nullable|array',
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
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $product->categories()->sync($validated['categories'] ?? []);

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

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
            $product->image = $mainImagePath;
            $product->save();

            $product->variants()->each(fn($variant) => $variant->delete());

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
            return redirect()->route('admins.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật sản phẩm ' . $product->id . ': ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi cập nhật.')->withInput();
        }
    }

    public function show(Product $product)
    {
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
            'relatedProducts', 
            'cartCount'
        ));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admins.products.index')->with('success', 'Đã xóa sản phẩm thành công.');
    }
}
