<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProductReviewController extends Controller
{
    // Hiển thị danh sách đánh giá/bình luận
    public function index()
    {
        $reviews = ProductReview::with(['user', 'product', 'parent'])
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admins.reviews.index', compact('reviews'));
    }

    // Xóa đánh giá/bình luận
    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->delete();
        return back()->with('success', 'Đã xóa bình luận/đánh giá!');
    }

    // Trả lời bình luận (admin trả lời như 1 review con)
    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        $parent = ProductReview::findOrFail($id);
        $reply = ProductReview::create([
            'user_id' => 1, // tạm thời gán admin id=1, sau này có thể sửa lại
            'product_id' => $parent->product_id,
            'content' => $request->content,
            'parent_id' => $parent->id,
            'stars' => null,
        ]);
        return back()->with('success', 'Đã trả lời bình luận!');
    }

    // Hiển thị form tạo mới đánh giá
    public function create()
    {
        $products = Product::all();
        $users = User::all();
        return view('admins.reviews.create', compact('products', 'users'));
    }

    // Lưu đánh giá mới
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'stars' => 'nullable|integer|min:1|max:5',
            'content' => 'required|string|max:1000',
        ]);
        ProductReview::create($request->only('user_id', 'product_id', 'stars', 'content'));
        return redirect()->route('admins.reviews.index')->with('success', 'Đã thêm đánh giá mới!');
    }

    // Hiển thị form chỉnh sửa đánh giá
    public function edit($id)
    {
        $review = ProductReview::findOrFail($id);
        $products = Product::all();
        $users = User::all();
        return view('admins.reviews.edit', compact('review', 'products', 'users'));
    }

    // Cập nhật đánh giá
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'stars' => 'nullable|integer|min:1|max:5',
            'content' => 'required|string|max:1000',
        ]);
        $review = ProductReview::findOrFail($id);
        $review->update($request->only('user_id', 'product_id', 'stars', 'content'));
        return redirect()->route('admins.reviews.index')->with('success', 'Đã cập nhật đánh giá!');
    }

    // Chuyển đổi trạng thái ẩn/hiện bình luận
    public function toggleHide($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->is_hidden = !$review->is_hidden;
        $review->save();
        return back()->with('success', 'Đã cập nhật trạng thái bình luận!');
    }
} 