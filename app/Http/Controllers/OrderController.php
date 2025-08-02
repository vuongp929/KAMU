<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Category;
use App\Models\OrderItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $categoriesForMenu = Category::whereNull('parent_id')->with('children')->get();

        // Lấy giỏ hàng hiện tại của user
        $cart = Cart::with('items.variant.product')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $cartCount = 0;
        foreach ($cart->items as $item) {
            $cartCount += $item->quantity;
        }
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        // Tính tổng tiền
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->quantity * ($item->variant->price ?? 0);
        }

        return view('clients.orders.index', [
            'cart' => $cart,
            'total_price' => $total,
            'user' => $user,
            'cartCount' => $cartCount,
            'categories' => $categoriesForMenu,
        ]);
    }

  public function store(Request $request)
{
    $user = Auth::user();

    DB::beginTransaction();

    try {
        // 1. Tạo đơn hàng
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'processing',
            'payment_status' => $request->input('payment_status', 'paid'),
            'total_price' => $request->input('total_price', 0),
            'shipping_address' => $request->input('shipping_address'),
            'payment_method' => $request->input('payment_method'),
        ]);

        // 2. Tạo các dòng chi tiết đơn hàng
        $cartItems = $request->input('cart_items', []);
        foreach ($cartItems as $item) {
            // dd($request->cart_items);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price_at_order'],
                'price_at_order' => $item['price_at_order'],
                'size' => $item['size'],
            ]);

        }

       if ($user->cart) {
            $user->cart->items()->delete();
            $user->cart->delete();
        }

        DB::commit();

        return redirect()->route('order.success')->with('success', 'Đặt hàng thành công!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Lỗi khi đặt hàng: ' . $e->getMessage());
    }
}
public function success()
{
    $categoriesForMenu = Category::whereNull('parent_id')->with('children')->get();
   $cartCount = 0;
      return view('clients.orders.success', [
           'categories' => $categoriesForMenu,
            'cartCount' => $cartCount,
        ]);
}
}
