<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Discount;
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
    public function index()
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        // Lấy giỏ hàng với thông tin sản phẩm
        $cart = Cart::with([
                        'items.variant.product.mainImage',
                        'items.variant.product.firstImage'
                    ])
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->first();

        // Nếu giỏ hàng rỗng, không cho vào checkout, chuyển về trang giỏ hàng
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Lấy các mã giảm giá đang hoạt động
        $vouchers = Discount::where('is_active', 1)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->get();

        // Tính subtotal từ cart items
        $subtotal = $cart->items->sum(function ($item) {
            $price = $item->price ?? ($item->variant ? $item->variant->price : 0);
            return $item->quantity * $price;
        });

        // Phí vận chuyển (có thể tùy chỉnh logic)
        $shipping_fee = 0; // Miễn phí vận chuyển
        
        // Giá trị giảm giá hiện tại (mặc định là 0)
        $discount = 0;

        return view('clients.checkout.index', compact('cart', 'vouchers', 'subtotal', 'shipping_fee', 'discount'));
    }

    /**
     * Xử lý logic đặt hàng.
     */
    public function placeOrder(Request $request)
    {
    // Debug: Log dữ liệu nhận được
    Log::info('Checkout data received:', $request->all());
    
    // 1. Validate (Giữ nguyên)
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'payment_method' => 'required|string|in:cod,vnpay,momo',
        'discount_code' => 'nullable|string',
        'discount_value' => 'nullable|numeric|min:0',
        'final_total' => 'nullable|numeric|min:0',
        'shipping_fee' => 'nullable|numeric|min:0',
    ]);

    $user = Auth::user();
    $cart = Cart::with('items.variant.product')->where('user_id', $user->id)->latest()->first();

    // 2. Kiểm tra lại giỏ hàng (Giữ nguyên)
    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đã hết hạn. Vui lòng thử lại.');
    }

    DB::beginTransaction();
    try {
        // 3. Tạo đơn hàng - sử dụng tổng tiền từ frontend (đã bao gồm phí vận chuyển)
        $finalTotal = $validated['final_total'] ?? $cart->total_price;
        $discountValue = $validated['discount_value'] ?? 0;
        $shippingFee = $validated['shipping_fee'] ?? 0;
        
        // Đảm bảo final_total bao gồm phí vận chuyển nếu chưa có
        if ($shippingFee > 0 && $finalTotal == $cart->total_price) {
            $finalTotal = $cart->total_price + $shippingFee - $discountValue;
        }
        
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
        
        // Tạo thông tin khách hàng dưới dạng JSON
        $customerInfo = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address']
        ];
        
        // Xác định trạng thái thanh toán dựa trên phương thức thanh toán
        $paymentStatus = 'unpaid'; // Mặc định
        if (in_array($validated['payment_method'], ['vnpay', 'momo'])) {
            $paymentStatus = 'awaiting_payment'; // Đang chờ thanh toán online
        } elseif ($validated['payment_method'] === 'cod') {
            $paymentStatus = 'cod'; // COD
        }
        
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $cart->total_price, // Giá gốc
            'final_total' => $finalTotal, // Giá sau giảm
            'discount_code' => $discountCode,
            'discount_amount' => $discountValue,
            'shipping_fee' => $shippingFee,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => $paymentStatus,
            'shipping_address' => json_encode($customerInfo), // Lưu thông tin khách hàng dưới dạng JSON
        ]);

        // Tạo order items
        foreach ($cart->items as $cartItem) {
            $variant = $cartItem->variant;
            if (!$variant) {
                throw new \Exception("Không tìm thấy biến thể sản phẩm.");
            }
            
            // Chỉ kiểm tra tồn kho cơ bản, không trừ ngay
            // Việc trừ tồn kho sẽ được thực hiện khi thanh toán thành công
            if ($variant->stock < $cartItem->quantity) {
                throw new \Exception("Sản phẩm \"{$variant->product->name}\" không đủ tồn kho. Còn lại: {$variant->stock}, yêu cầu: {$cartItem->quantity}");
            }
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $variant->product_id,
                'product_variant_id' => $cartItem->product_variant_id,
                'quantity' => $cartItem->quantity,
                'price' => $variant->price,
            ]);
        }

            DB::commit();

            // === LOGIC PHÂN LUỒNG QUAN TRỌNG ===
            $paymentMethod = $order->payment_method;

            if ($paymentMethod === 'vnpay') {
                return redirect()->route('payment.vnpay.create', ['orderId' => $order->id]);
            }
            
            if ($paymentMethod === 'momo') {
                return redirect()->route('payment.momo.create', ['orderId' => $order->id]);
            }

            // Mặc định là COD - trừ tồn kho ngay khi đặt hàng thành công
            // Kiểm tra tồn kho trước khi trừ để tránh overselling
            foreach ($order->items as $orderItem) {
                $variant = $orderItem->variant;
                if (!$variant) {
                    throw new \Exception("Không tìm thấy biến thể sản phẩm cho đơn hàng #{$order->id}");
                }
                
                if ($variant->stock < $orderItem->quantity) {
                    throw new \Exception("Sản phẩm '{$variant->product->name}' đã hết hàng. Còn lại: {$variant->stock}, yêu cầu: {$orderItem->quantity}. Vui lòng chọn mặt hàng khác.");
                }
            }
            
            // Nếu tất cả sản phẩm đều đủ tồn kho, tiến hành trừ
            foreach ($order->items as $orderItem) {
                $variant = $orderItem->variant;
                $variant->decrement('stock', $orderItem->quantity);
            }
            
            $order->status = 'processing';
            $order->save();
            $cart->delete();
            
            try {
                $order->load('items.variant.product');
                $confirmationUrl = URL::temporarySignedRoute('client.orders.confirm', now()->addHours(48), ['order' => $order->id]);
                Mail::to($order->email)->send(new OrderConfirmationMail($order, $confirmationUrl));
            } catch (\Exception $e) {
                Log::warning("Gửi email cho đơn hàng COD #{$order->id} thất bại: " . $e->getMessage());
            }
            
            return redirect()->route('client.orders.show', $order)->with('success', 'Đặt hàng COD thành công!');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi đặt hàng: ' . $e->getMessage());
            return back()->with('error', "Đã xảy ra lỗi: " . $e->getMessage())->withInput();
        }
    }
}