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
use App\Models\Discount;


class CheckoutController extends Controller
{
    /**
     * Hiển thị trang checkout với thông tin giỏ hàng.
     */
// trong app/Http\Controllers\Client\CheckoutController.php

public function index()
{
    // Kiểm tra user đã đăng nhập chưa
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
    }

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

    // Lấy các mã giảm giá đang hoạt động
    $discounts = Discount::where('is_active', 1)
        ->where('start_at', '<=', now())
        ->where('end_at', '>=', now())
        ->get();

    return view('clients.checkout.index', compact('cart', 'discounts'));
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
        'discount_code' => 'nullable|string',
        'discount_value' => 'nullable|numeric|min:0',
        'final_total' => 'nullable|numeric|min:0',
    ]);

    $user = Auth::user();
    $cart = Cart::with('items.variant.product')->where('user_id', $user->id)->latest()->first();

    // 2. Kiểm tra lại giỏ hàng (Giữ nguyên)
    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đã hết hạn. Vui lòng thử lại.');
    }

    DB::beginTransaction();
    try {
        // 3. Tạo đơn hàng - sử dụng tổng tiền đã được giảm giá
        $finalTotal = $validated['final_total'] ?? $cart->total_price;
        $discountValue = $validated['discount_value'] ?? 0;
        
        // Kiểm tra và cập nhật mã giảm giá nếu có
        $discountCode = $validated['discount_code'] ?? null;
        $discount = null;
        if ($discountCode) {
            $discount = Discount::where('code', $discountCode)->first();
            if ($discount && $discount->isValid()) {
                // Tăng số lần sử dụng
                $discount->incrementUsageCount();
            }
        }
        
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $finalTotal, // Sử dụng tổng tiền đã được giảm giá
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'unpaid',
            'shipping_address' => $validated['address'], // Sử dụng shipping_address thay vì address
        ]);

        // 4. Chuyển item và trừ tồn kho (Giữ nguyên)
        foreach ($cart->items as $cartItem) {
            $variant = $cartItem->variant;
            if (!$variant || $variant->stock < $cartItem->quantity) {
                $productName = $variant && $variant->product ? $variant->product->name : 'Sản phẩm không tồn tại';
                $variantName = $variant ? $variant->name : 'N/A';
                throw new \Exception("Sản phẩm \"{$productName} - {$variantName}\" không đủ số lượng tồn kho.");
            }
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $variant->product_id ?? $cartItem->product_id,
                'product_variant_id' => $cartItem->product_variant_id,
                'quantity' => $cartItem->quantity,
                'price_at_order' => $cartItem->price_at_order, // Sử dụng giá đã lưu trong giỏ hàng
                'price' => $variant->price ?? 0, // Giá hiện tại của variant
            ]);
            if ($variant) {
                $variant->decrement('stock', $cartItem->quantity);
            }
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

            // 2. Gửi email với Mailable đã được cập nhật - sử dụng email từ form
            Mail::to($validated['email'])->send(new OrderConfirmationMail($order, $confirmationUrl));

        } catch (\Exception $e) {
            Log::warning("Gửi email cho đơn hàng #{$order->id} thất bại: " . $e->getMessage());
        }
        
        // Chuyển hướng người dùng với thông báo chi tiết
        $successMessage = "🎉 Đặt hàng thành công!\n\n";
        $successMessage .= "📋 Mã đơn hàng: #{$order->id}\n";
        $successMessage .= "💰 Tổng tiền: " . number_format($finalTotal, 0, ',', '.') . " VNĐ\n";
        $successMessage .= "📧 Email xác nhận đã được gửi đến: {$validated['email']}\n\n";
        $successMessage .= "📱 Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận đơn hàng!";
        
        return redirect()->route('home')->with('success', $successMessage);

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Lỗi khi đặt hàng: ' . $e->getMessage());
        
        $errorMessage = "❌ Đặt hàng thất bại!\n\n";
        $errorMessage .= "🔍 Lỗi: " . $e->getMessage() . "\n\n";
        $errorMessage .= "📞 Vui lòng liên hệ hỗ trợ nếu vấn đề vẫn tiếp tục.";
        
        return back()->with('error', $errorMessage)->withInput();
    }
}
}