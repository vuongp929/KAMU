<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Hiển thị danh sách đơn hàng
public function index()
{
    $categoriesForMenu = Category::whereNull('parent_id')->with('children')->get();

    $user = Auth::user();
    $cartCount = 0;
    $total = 0;
    $carts = null;

    if ($user) {
        // ✅ Lấy tất cả giỏ hàng của user kèm item, variant và product
      $carts = Cart::with('items.variant.product')
             ->where('user_id', $user->id)
             ->orderBy('created_at', 'desc')
             ->get();

        if ($carts->isNotEmpty()) {
            foreach ($carts as $cart) {
                foreach ($cart->items as $item) {
                    $cartCount += $item->quantity;

                    // ✅ Ưu tiên dùng price_at_order nếu có, nếu không thì fallback về variant->price
                    $price = $item->price_at_order ?? ($item->variant->price ?? 0);
                    //  dd($item->variant->product->name); 


                    $total += $item->quantity * $price;
                }
            }
        }
    }
                  
  
    return view('clients.cart.index', [
        'categories' => $categoriesForMenu,
        'cartCount' => $cartCount,
        'carts' => $carts,
        'total' => $total,  
    ]);
}




    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with(['orderItems.productVariant.product', 'customer'])->findOrFail($id);
        return view('admins.orders.show', compact('order'));
    }


    public function updateTotalPrice(Order $order)
    {
        $total = $order->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $order->total_price = $total;
        $order->save();
    }

    // Cập nhật thông tin đơn hàng (trạng thái, trạng thái thanh toán, v.v.)
    // public function update(Request $request, $id)
    // {
    //     $order = Order::with('orderItems')->findOrFail($id); // Load cả orderItems để tính toán

    //     // Cập nhật trạng thái hoặc trạng thái thanh toán
    //     if ($request->has('status')) {
    //         $order->status = $request->status;
    //     }

    //     if ($request->has('payment_status')) {
    //         $order->payment_status = $request->payment_status;
    //     }

    //     // Tính toán lại total_price dựa trên orderItems
    //     $total = $order->orderItems->sum(function ($item) {
    //         return $item->quantity * $item->price;
    //     });

    //     $order->total_price = $total;

    //     // Lưu lại các thay đổi
    //     $order->save();

    //     return redirect()->route('orders.index')->with('success', 'Thông tin đơn hàng đã được cập nhật.');
    // }
    
    public function placeOrder(Request $request)
    {
        // Lưu thông tin đơn hàng vào DB
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $request->cartTotal,
            'status' => 'pending',
        ]);

        return redirect()->route('payment.vnpay.qr', ['orderId' => $order->id]);
    }
    // Hủy đơn hàng
    public function cancel($id)
    {
        // Tìm đơn hàng theo ID
        $order = Order::findOrFail($id);

        // Kiểm tra trạng thái của đơn hàng có thể hủy hay không
        if ($order->status != 'Đang chờ xử lý') {
            return redirect()->route('client.orders.index')->with('error', 'Đơn hàng không thể hủy vì trạng thái hiện tại.');
        }

        // Cập nhật trạng thái đơn hàng thành "Đã hủy"
        $order->status = 'Đã hủy';
        $order->save();

        // Nếu có các món hàng (OrderItem) liên quan, cũng cập nhật trạng thái của chúng thành "Đã hủy"
        foreach ($order->orderItems as $orderItem) {
            $orderItem->status = 'Đã hủy';
            $orderItem->save();
        }

        // Thông báo cho người dùng
        return redirect()->route('client.orders.index')->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    public function remove(Request $request)
{
    $user = Auth::user();

    // Kiểm tra user đăng nhập và có truyền product_variant_id
    if (!$user || !$request->product_variant_id) {
        return redirect()->back()->with('error', 'Không thể xóa sản phẩm.');
    }

    // Lấy giỏ hàng mới nhất của user
    $cart = Cart::where('user_id', $user->id)->latest()->first();

    if ($cart) {
        // Tìm cart item theo variant id
        $cartItem = $cart->items()->where('product_variant_id', $request->product_variant_id)->first();

        if ($cartItem) {
            $cartItem->delete();
            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }
    }

    return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng.');
}
public function update(Request $request)
{
    $user = Auth::user();

    $variantId = $request->product_variant_id;
    $quantity = max((int) $request->quantity, 1);

    $cart = Cart::where('user_id', $user->id)->latest()->first();

    if ($cart) {
        $item = $cart->items()->where('product_variant_id', $variantId)->first();
        if ($item) {
            $item->update(['quantity' => $quantity]);
        }
    }

    return redirect()->back(); // hoặc response()->json() nếu dùng AJAX
}
}
