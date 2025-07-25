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

class CheckoutController extends Controller
{
    /**
     * Hiển thị trang checkout.
     */
    public function index()
    {
        // Lấy giỏ hàng của người dùng đã đăng nhập
        $cart = Cart::with('items.variant.product')
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->first();

        // Nếu giỏ hàng rỗng, không cho vào checkout, chuyển về trang giỏ hàng
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        return view('clients.checkout.index', compact('cart'));
    }

    /**
     * Xử lý việc đặt hàng.
     */
    public function placeOrder(Request $request)
    {
        // 1. Validate thông tin giao hàng
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|string', // Ví dụ: 'cod', 'vnpay'
        ]);

        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->latest()->first();

        // 2. Kiểm tra lại giỏ hàng một lần nữa
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('home')->with('error', 'Đã có lỗi xảy ra với giỏ hàng của bạn.');
        }

        DB::beginTransaction();
        try {
            // 3. Tạo đơn hàng (Order) mới
            $order = Order::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'total_price' => $cart->total_price, // Lấy tổng tiền từ accessor của Cart
                'status' => 'pending', // Trạng thái ban đầu
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'unpaid', // Trạng thái thanh toán ban đầu
            ]);

            // 4. Chuyển các sản phẩm từ CartItem sang OrderItem
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->variant->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price_at_order ?? $cartItem->variant->price,
                ]);
                
                 $variant = $cartItem->variant;
                if ($variant->stock < $cartItem->quantity) {
                    // Ném ra một Exception để hủy toàn bộ transaction
                    throw new \Exception("Sản phẩm {$variant->product->name} - {$variant->name} không đủ tồn kho.");
                }
                $variant->stock -= $cartItem->quantity;
                $variant->save();
            }

            // 5. Xóa giỏ hàng sau khi đã đặt hàng thành công
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            // 6. Xử lý sau khi đặt hàng
            if ($order->payment_method == 'vnpay') {
                // TODO: Chuyển hướng đến trang thanh toán VNPay
                // return redirect()->route('payment.vnpay.create', ['order' => $order]);
                return redirect()->route('home')->with('success', 'Chuyển đến trang thanh toán VNPay (chưa cài đặt).');
            }
            
            // Mặc định là COD
            // TODO: Gửi email xác nhận đơn hàng
            return redirect()->route('home')->with('success', 'Đặt hàng thành công! Cảm ơn bạn đã mua sắm.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi đặt hàng: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi không mong muốn khi đặt hàng. Vui lòng thử lại.')->withInput();
        }
    }
}