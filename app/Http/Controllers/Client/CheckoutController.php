<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\OrderConfirmationMail;


class CheckoutController extends Controller
{
    /**
     * Hiển thị trang checkout với thông tin giỏ hàng.
     */
// trong app/Http\Controllers\Client\CheckoutController.php

public function index()
{
    // === BẮT ĐẦU SỬA LỖI ===
    // Thay thế 'thumbnail' bằng 'mainImage' và 'firstImage'
    $cart = Cart::with([
                    'items.variant.product.mainImage',
                    'items.variant.product.firstImage'
                ])
                ->where('user_id', Auth::id())
                ->latest()
                ->first();
    // === KẾT THÚC SỬA LỖI ===

    // Nếu giỏ hàng rỗng, không cho vào checkout, chuyển về trang giỏ hàng
    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
    }

    return view('clients.checkout.index', compact('cart'));
}

    /**
     * Xử lý logic đặt hàng.
     */
    public function placeOrder(Request $request)
{
    // 1. Validate (Giữ nguyên)
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'payment_method' => 'required|string|in:cod,vnpay',
    ]);

    $user = Auth::user();
    $cart = Cart::with('items.variant.product')->where('user_id', $user->id)->latest()->first();

    // 2. Kiểm tra lại giỏ hàng (Giữ nguyên)
    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đã hết hạn. Vui lòng thử lại.');
    }

    DB::beginTransaction();
    try {
        // 3. Tạo đơn hàng (Giữ nguyên)
        $order = Order::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'total_price' => $cart->total_price,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'unpaid',
        ]);

        // 4. Chuyển item và trừ tồn kho (Giữ nguyên)
        foreach ($cart->items as $cartItem) {
            $variant = $cartItem->variant;
            if (!$variant || $variant->stock < $cartItem->quantity) {
                throw new \Exception("Sản phẩm \"{$variant->product->name} - {$variant->name}\" không đủ số lượng tồn kho.");
            }
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $variant->product_id,
                'product_variant_id' => $cartItem->product_variant_id,
                'quantity' => $cartItem->quantity,
                'price' => $variant->price,
            ]);
            $variant->decrement('stock', $cartItem->quantity);
        }

        // 5. Xóa giỏ hàng (Giữ nguyên)
        $cart->delete();

        DB::commit();

        $order->load('items.variant.product');

        
        try {
            // 1. Tạo URL xác nhận có chữ ký, hết hạn sau 48 giờ
            $confirmationUrl = URL::temporarySignedRoute(
                'client.orders.confirm', now()->addHours(48), ['order' => $order->id]
            );

            // 2. Gửi email với Mailable đã được cập nhật
            Mail::to($order->email)->send(new OrderConfirmationMail($order, $confirmationUrl));

        } catch (\Exception $e) {
            Log::warning("Gửi email cho đơn hàng #{$order->id} thất bại: " . $e->getMessage());
        }
        
        // Chuyển hướng người dùng
        return redirect()->route('home')->with('success', 'Đặt hàng thành công! Vui lòng kiểm tra email để xác nhận đơn hàng của bạn.');

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Lỗi khi đặt hàng: ' . $e->getMessage());
        return back()->with('error', $e->getMessage())->withInput();
    }
}
}