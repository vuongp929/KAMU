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
    /**
     * Xử lý logic đặt hàng, tạo đơn hàng và chuyển hướng thanh toán.
     */
    public function placeOrder(Request $request)
    {
        // 1. Validate dữ liệu người dùng nhập từ form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cod,vnpay,momo',
            'discount_code' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $cart = Cart::with('items.variant.product')->where('user_id', $user->id)->latest()->first();

        // 2. Kiểm tra lại giỏ hàng để đảm bảo an toàn
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đã hết hạn hoặc có lỗi. Vui lòng thử lại.');
        }

        // 3. Tính toán lại tổng tiền cuối cùng dựa trên mã giảm giá (nếu có)
        $finalTotal = $cart->total_price;
        if (!empty($validated['discount_code'])) {
            $discountResult = $this->applyDiscountCode($validated['discount_code'], $user, $cart->total_price);
            
            // Nếu mã không hợp lệ, quay lại với thông báo lỗi
            if (!$discountResult['success']) {
                return back()->with('error', $discountResult['message'])->withInput();
            }
            // Nếu hợp lệ, cập nhật tổng tiền cuối cùng
            $finalTotal = $cart->total_price - $discountResult['discount_amount'];
        }

        // Bắt đầu một transaction để đảm bảo tất cả các thao tác đều thành công
        DB::beginTransaction();
        try {
            // 4. Tạo bản ghi đơn hàng (Order) trong database
            $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $finalTotal,
            'status' => 'pending',
            'payment_method' => trim($validated['payment_method']),
            'payment_status' => 'unpaid',
            'shipping_address' => json_encode([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]),
            'discount_code' => !empty($validated['discount_code']) ? $validated['discount_code'] : null,
        ]);

            // 5. Chuyển các sản phẩm từ giỏ hàng sang chi tiết đơn hàng (OrderItem)
            foreach ($cart->items as $cartItem) {
                $variant = $cartItem->variant;
                // Kiểm tra tồn kho
                if (!$variant || $variant->stock < $cartItem->quantity) {
                    throw new \Exception("Sản phẩm \"{$variant->product->name} - {$variant->name}\" không đủ số lượng tồn kho.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $variant->price, // Lấy giá hiện tại để đảm bảo chính xác
                ]);
                
                // Trừ tồn kho
                $variant->decrement('stock', $cartItem->quantity);
            }

            // 6. Cập nhật trạng thái mã giảm giá (nếu có)
            if (!empty($validated['discount_code'])) {
                $discount = Discount::where('code', $validated['discount_code'])->first();
                if ($discount) $discount->increment('used_count');
                
                $userDiscountCode = UserDiscountCode::where('discount_code', $validated['discount_code'])->where('user_id', $user->id)->first();
                if ($userDiscountCode) $userDiscountCode->markAsUsed();
            }

            // Nếu tất cả các bước trên thành công, lưu lại transaction
            DB::commit();

            // ==========================================================
            // === LOGIC PHÂN LUỒNG SAU KHI TẠO ĐƠN HÀNG THÀNH CÔNG ===
            // ==========================================================

            $paymentMethod = $order->payment_method;

            // Chuyển hướng thanh toán online
            if ($paymentMethod === 'vnpay') {
                    // dd('Đã vào đúng khối IF của VNPay. Chuẩn bị chuyển hướng.', $order->toArray());

                return redirect()->route('payment.vnpay.create', ['orderId' => $order->id]);
            }
            if ($paymentMethod === 'momo') {
                
                return redirect()->route('payment.momo.create', ['orderId' => $order->id]);
            }

            // --- Mặc định là xử lý cho COD ---
            $order->status = 'processing';
            $order->save();
            $cart->delete(); // Xóa giỏ hàng ngay lập tức cho COD

            // Gửi email xác nhận
            try {
                $order->load('items.variant.product');
                $confirmationUrl = URL::temporarySignedRoute('client.orders.confirm', now()->addHours(48), ['order' => $order->id]);
                Mail::to($order->email)->send(new OrderConfirmationMail($order, $confirmationUrl));
            } catch (\Exception $e) {
                Log::warning("Gửi email cho đơn hàng COD #{$order->id} thất bại: " . $e->getMessage());
            }
            
            return redirect()->route('client.orders.show', $order)->with('success', 'Đặt hàng COD thành công! Vui lòng kiểm tra email để xác nhận.');

        } catch (\Throwable $e) {
            DB::rollBack(); // Hoàn tác tất cả các thay đổi trong DB nếu có lỗi
            Log::error('Lỗi khi đặt hàng: ' . $e->getMessage());
            return back()->with('error', "Đã xảy ra lỗi: " . $e->getMessage())->withInput();
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