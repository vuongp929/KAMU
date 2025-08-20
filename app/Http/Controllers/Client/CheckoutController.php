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
            'total_price' => $cart->total_price, // Giá gốc
            'final_total' => $finalTotal, // Giá sau giảm
            'discount_code' => $discountCode,
            'discount_amount' => $discountValue,
            'shipping_fee' => $validated['shipping_fee'] ?? 0,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'unpaid',
            'shipping_address' => $validated['address'], // Sử dụng shipping_address thay vì address
        ]);

        // Tạo order items
        foreach ($cart->items as $cartItem) {
            $variant = $cartItem->variant;
            if (!$variant || $variant->stock < $cartItem->quantity) {
                throw new \Exception("Sản phẩm \"{$variant->product->name}\" không đủ tồn kho.");
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

            DB::commit();

            // === LOGIC PHÂN LUỒNG QUAN TRỌNG ===
            $paymentMethod = $order->payment_method;

            if ($paymentMethod === 'vnpay') {
                return redirect()->route('payment.vnpay.create', ['orderId' => $order->id]);
            }
            
            if ($paymentMethod === 'momo') {
                return redirect()->route('payment.momo.create', ['orderId' => $order->id]);
            }

            // Mặc định là COD      
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