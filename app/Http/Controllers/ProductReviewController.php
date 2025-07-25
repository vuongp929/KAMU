<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    // Lưu đánh giá mới
    public function store(Request $request, $productId)
    {
        $request->validate([
            'stars' => 'nullable|integer|min:1|max:5',
            'content' => 'required|string|max:1000',
        ]);
        $product = Product::findOrFail($productId);
        ProductReview::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'stars' => $request->stars,
            'content' => $request->content,
            'parent_id' => null,
        ]);
        return back()->with('success', 'Đánh giá của bạn đã được gửi!');
    }

    // Trả lời bình luận
    public function reply(Request $request, $productId, $parentId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        $product = Product::findOrFail($productId);
        $parent = ProductReview::findOrFail($parentId);
        ProductReview::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'content' => $request->content,
            'parent_id' => $parent->id,
            'stars' => null,
        ]);
        return back()->with('success', 'Bình luận trả lời đã được gửi!');
    }

    // Xóa đánh giá của user
    public function destroy($productId, $reviewId)
    {
        $review = ProductReview::findOrFail($reviewId);
        if ($review->user_id !== auth()->id()) {
            abort(403, 'Bạn chỉ có thể xóa đánh giá của chính mình.');
        }
        $review->delete();
        return response()->json(['success' => true]);
    }
} 