<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Discount;
use App\Models\UserDiscountCode;
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
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
    }

    $cart = Cart::with([
                    'items.variant.product.mainImage',
                    'items.variant.product.firstImage'
                ])
                ->where('user_id', Auth::id())
                ->latest()
                ->first();

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
    // 1. Validate
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'payment_method' => 'required|string|in:cod,vnpay,momo',
        'discount_code' => 'nullable|string|max:255',
        'discount_value' => 'nullable|numeric|min:0',
        'final_total' => 'nullable|numeric|min:0',
    ]);

    $user = Auth::user();
    $cart = Cart::with('items.variant.product')->where('user_id', $user->id)->latest()->first();

    // 2. Kiểm tra lại giỏ hàng
    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đã hết hạn. Vui lòng thử lại.');
    }

    // 3. Xử lý mã giảm giá nếu có
    $discountAmount = 0;
    $discountCode = null;
    $finalTotal = $cart->total_price;

    if (!empty($validated['discount_code'])) {
        $discountResult = $this->applyDiscountCode($validated['discount_code'], $user, $cart->total_price);
        
        if ($discountResult['success']) {
            $discountAmount = $discountResult['discount_amount'];
            $discountCode = $discountResult['discount_code'];
            $finalTotal = $cart->total_price - $discountAmount;
        } else {
            return back()->with('error', $discountResult['message'])->withInput();
        }
    }

    DB::beginTransaction();
    try {
        // 4. Tạo đơn hàng
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $finalTotal, // Sử dụng giá sau khi áp dụng mã giảm giá
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'unpaid',
            'shipping_address' => json_encode([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]),
        ]);
        $finalTotal = $validated['final_total'] ?? $cart->total_price;
        $discountValue = $validated['discount_value'] ?? 0;
        
        $discountCode = $validated['discount_code'] ?? null;
        $discount = null;
        if ($discountCode) {
            $discount = Discount::where('code', $discountCode)->first();
            if ($discount && $discount->isValid()) {
                // Tăng số lần sử dụng
                $discount->incrementUsageCount();
            }
        }

        // 5. Chuyển item và trừ tồn kho
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
                'price_at_order' => $cartItem->price_at_order,
                'price' => $variant->price ?? 0,
            ]);
            if ($variant) {
                $variant->decrement('stock', $cartItem->quantity);
            }
        }

        // 6. Xóa giỏ hàng
        $cart->delete();

        // 7. Đánh dấu mã giảm giá đã sử dụng nếu có
        if ($discountCode) {
            $userDiscountCode = UserDiscountCode::where('discount_code', $discountCode)->first();
            if ($userDiscountCode) {
                $userDiscountCode->markAsUsed();
            }
        }

        DB::commit();
        dd(
            "Dữ liệu từ form:",
            $request->all(), // Xem tất cả dữ liệu form gửi lên
            "Phương thức thanh toán đã lưu vào đơn hàng:",
            $order->payment_method
        );
        $order->load('items.variant.product');

        
        try {
            // 1. Tạo URL xác nhận có chữ ký, hết hạn sau 48 giờ
            $confirmationUrl = URL::temporarySignedRoute(
                'client.orders.confirm', now()->addHours(48), ['order' => $order->id]
            );

            // 2. Gửi email với Mailable đã được cập nhật
            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order, $confirmationUrl));

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

    /**
     * Validate mã giảm giá (AJAX)
     */
    public function validateDiscount(Request $request)
    {
        $request->validate([
            'discount_code' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $result = $this->applyDiscountCode($request->discount_code, $user, $request->total_amount);
        
        return response()->json($result);
    }

    /**
     * Áp dụng mã giảm giá
     */
    private function applyDiscountCode($code, $user, $totalAmount)
    {
        // 1. Kiểm tra mã đổi thưởng (UserDiscountCode) trước
        $userDiscountCode = UserDiscountCode::where('discount_code', $code)
                                           ->where('user_id', $user->id)
                                           ->where('is_used', false)
                                           ->where('expires_at', '>', now())
                                           ->first();

        if ($userDiscountCode) {
            // Kiểm tra giá trị đơn hàng tối thiểu (100,000 VND cho mã đổi thưởng)
            if ($totalAmount < 100000) {
                return [
                    'success' => false,
                    'message' => 'Mã đổi thưởng chỉ áp dụng cho đơn hàng từ 100,000 VND.'
                ];
            }

            // Tính toán số tiền giảm giá từ mã đổi thưởng
            $discountPercentage = $userDiscountCode->discount_percentage * 100; // Chuyển về phần trăm
            $discountAmount = ($totalAmount * $discountPercentage) / 100;

            return [
                'success' => true,
                'discount_amount' => $discountAmount,
                'discount_code' => $code,
                'message' => "Áp dụng mã đổi thưởng thành công! Giảm {$discountPercentage}%"
            ];
        }

        // 2. Kiểm tra mã giảm giá thông thường trong bảng discounts
        $discount = Discount::where('code', $code)
                           ->where('is_active', true)
                           ->where('start_at', '<=', now())
                           ->where('end_at', '>=', now())
                           ->first();

        if (!$discount) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'
            ];
        }

        // Kiểm tra số lần sử dụng
        if ($discount->max_uses && $discount->used_count >= $discount->max_uses) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá đã hết lượt sử dụng.'
            ];
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($totalAmount < $discount->min_order_amount) {
            return [
                'success' => false,
                'message' => 'Đơn hàng phải có giá trị tối thiểu ' . number_format($discount->min_order_amount) . ' VND.'
            ];
        }

        // Kiểm tra xem người dùng đã sử dụng mã này chưa (nếu once_per_order = true)
        if ($discount->once_per_order) {
            $usedDiscount = UserDiscountCode::where('discount_code', $code)
                                           ->where('user_id', $user->id)
                                           ->where('is_used', true)
                                           ->first();
            
            if ($usedDiscount) {
                return [
                    'success' => false,
                    'message' => 'Bạn đã sử dụng mã giảm giá này trước đó.'
                ];
            }
        }

        // Tính toán số tiền giảm giá
        $discountAmount = 0;
        if ($discount->discount_type === 'percent') {
            $discountAmount = ($totalAmount * $discount->discount) / 100;
        } else {
            $discountAmount = $discount->amount;
        }

        // Cập nhật số lần sử dụng
        $discount->increment('used_count');

        return [
            'success' => true,
            'discount_amount' => $discountAmount,
            'discount_code' => $code,
            'message' => 'Áp dụng mã giảm giá thành công!'
        ];
    }
}