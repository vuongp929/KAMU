<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Hiển thị trang giỏ hàng.
     */
    public function index()
    {
        // === BẮT ĐẦU SỬA LỖI ===
        // Tải trước các mối quan hệ với tên đúng: mainImage và firstImage
        $cart = Cart::with([
            // Tải các item của giỏ hàng
            'items',
            // Với mỗi item, tải biến thể của nó
            'items.variant',
            // Với mỗi biến thể, tải sản phẩm cha của nó
            'items.variant.product',
            // Với mỗi sản phẩm, tải ảnh chính và ảnh đầu tiên của nó
            'items.variant.product.mainImage',
            'items.variant.product.firstImage',
        ])
        ->where('user_id', Auth::id())
        ->latest()
        ->first();
        // === KẾT THÚC SỬA LỖI ===

        // Giờ bạn có thể truyền thẳng biến $cart sang view
        // View sẽ tự tính tổng tiền và số lượng
        return view('clients.cart.index', compact('cart'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng.
     */
    public function add(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $variant = ProductVariant::find($validated['variant_id']);

        // Kiểm tra tồn kho
        if ($variant->stock < $validated['quantity']) {
            return back()->with('error', 'Số lượng sản phẩm trong kho không đủ.');
        }

        // 2. Tìm hoặc tạo giỏ hàng cho người dùng
        // firstOrCreate sẽ tìm giỏ hàng của user, nếu không có sẽ tự tạo mới
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // 3. Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $cartItem = $cart->items()->where('product_variant_id', $variant->id)->first();

        if ($cartItem) {
            // Nếu đã có, chỉ cập nhật số lượng
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            // Nếu chưa có, tạo một item mới trong giỏ hàng
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $validated['quantity'],
                'price_at_order' => $variant->price, // Lưu lại giá tại thời điểm thêm vào giỏ
            ]);
        }

        // 4. Chuyển hướng về trang giỏ hàng với thông báo thành công
        return redirect()->route('client.cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }


    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng.
     */
    public function update(Request $request)
    {
        $cart = Cart::where('user_id', Auth::id())->latest()->first();

        // Code này bây giờ sẽ hoạt động đúng
        if ($cart && $request->has('quantities')) {
            foreach ($request->quantities as $cartItemId => $quantity) {
                $item = $cart->items()->find($cartItemId);
                if ($item && $quantity > 0) {
                    $item->update(['quantity' => (int)$quantity]);
                }
            }
        }
        
        return back()->with('success', 'Giỏ hàng đã được cập nhật.');
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng.
     */
    public function remove($cartItemId)
    {
        $cart = Cart::where('user_id', Auth::id())->latest()->first();
        $item = $cart->items()->find($cartItemId);

        if ($item) {
            $item->delete();
            return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }

        return back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng.');
    }
}