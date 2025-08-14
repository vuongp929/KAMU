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
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }
        $cart = Cart::with(['items.variant.product.mainImage', 'items.variant.product.firstImage'])
                    ->where('user_id', Auth::id())->latest()->first();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }
        return view('clients.checkout.index', compact('cart'));
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cod,vnpay,momo',
        ]);

        $user = Auth::user();
        $cart = Cart::with('items.variant.product')->where('user_id', $user->id)->latest()->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đã hết hạn.');
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'total_price' => $cart->total_price,
                'status' => 'pending',
                'payment_method' => trim($validated['payment_method']),
                'payment_status' => 'unpaid',
            ]);

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