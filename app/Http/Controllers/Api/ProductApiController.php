<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Trả về dữ liệu chi tiết của một sản phẩm dưới dạng JSON.
     */
    public function show(Product $product)
    {
        // Tải tất cả các mối quan hệ cần thiết
        $product->load([
            'categories:name',
            'images',
            'variants.attributeValues.attribute',
            'variants.images'
        ]);

        // Trả về dữ liệu JSON
        return response()->json($product);
    }
}