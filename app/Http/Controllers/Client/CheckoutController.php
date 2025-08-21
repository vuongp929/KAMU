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
        // 1. Validate dữ liệu người dùng nhập
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|integer',
            'district_id' => 'required|integer',
            'ward_code' => 'required|string',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cod,vnpay,momo',
            'discount_code' => 'nullable|string', // Chỉ nhận mã code từ form
        ]);

        $user = Auth::user();
        $cart = Cart::with('items.variant.product')->where('user_id', $user->id)->latest()->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đã hết hạn.');
        }
        
        // --- BẮT ĐẦU TÍNH TOÁN LẠI MỌI THỨ Ở SERVER ---
        $subtotal = $cart->total_price;
        $shippingFee = session('shipping_fee', 0); // Lấy phí ship đã được tính và lưu vào session
        $discountAmount = 0;
        $discount = null;
        
        if (!empty($validated['discount_code'])) {
            $discount = Discount::where('code', $validated['discount_code'])->first();
            // Kiểm tra lại mã giảm giá một lần nữa để đảm bảo an toàn
            if ($discount && $discount->isValidFor($subtotal, $user)) {
                $discountAmount = $discount->calculateDiscount($subtotal);
            }
        }
        $finalTotal = ($subtotal + $shippingFee) - $discountAmount;
        // --- KẾT THÚC TÍNH TOÁN ---

        DB::beginTransaction();
        try {
            // Lấy tên địa chỉ từ ID/Code để lưu vào DB
            // TODO: Bạn cần có model và logic để lấy tên từ ID/Code
            $fullAddress = "{$validated['address']}, {$validated['ward_code']}, {$validated['district_id']}, {$validated['province_id']}";

            // 4. TẠO ĐƠN HÀNG (ĐÃ SỬA LẠI)
            // Chỉ lưu vào các cột thực sự có trong bảng 'orders'
            $order = Order::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $fullAddress,
                'total_price' => $finalTotal, // <-- Lưu giá cuối cùng vào cột total_price
                'status' => 'pending',
                'payment_method' => trim($validated['payment_method']),
                'payment_status' => 'unpaid',
                'discount_code' => $discount ? $discount->code : null,
                // Nếu bạn có các cột này trong DB, hãy thêm chúng vào
                // 'subtotal' => $subtotal,
                // 'shipping_fee' => $shippingFee,
                // 'discount_amount' => $discountAmount,
            ]);

            // 5. TẠO CHI TIẾT ĐƠN HÀNG VÀ TRỪ TỒN KHO
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

            // 6. Cập nhật số lần sử dụng mã giảm giá
            if ($discount) {
                $discount->increment('used_count');
                // Xử lý cả UserDiscountCode nếu cần
            }
            
            DB::commit();

            // 7. PHÂN LUỒNG THANH TOÁN
            $paymentMethod = $order->payment_method;

            if ($paymentMethod === 'vnpay') { return redirect()->route('payment.vnpay.create', ['orderId' => $order->id]); }
            if ($paymentMethod === 'momo') { return redirect()->route('payment.momo.create', ['orderId' => $order->id]); }
            
            // 8. XỬ LÝ COD
            $order->status = 'processing';
            $order->save();
            $cart->delete(); // Xóa giỏ hàng chỉ khi là COD
            
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