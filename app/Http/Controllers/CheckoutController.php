<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Category;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $categoriesForMenu = Category::whereNull('parent_id')->with('children')->get();

        $user = Auth::user();
        $cartCount = 0;
        $carts = null;

        if ($user) {
            // ✅ Lấy tất cả giỏ hàng của user kèm item, variant và product

            $orders = Order::with('items.productVariant.product')
                ->where('user_id', $user->id)
                ->where('status', '!=', 'delivered') // chỉ loại "đã giao"
                ->orderBy('created_at', 'desc')
                ->get();
        }


        return view('clients.checkout.index', [
            'categories' => $categoriesForMenu,
            'cartCount' => $cartCount,
            'checkouts' => $orders,
        ]);
    }
    public function show($id)
    {
        $categoriesForMenu = Category::whereNull('parent_id')->with('children')->get();

        $user = Auth::user();
        $cartCount = 0;

        $checkout = Order::with([
            'items.productVariant.product',
            'user'
        ])->where('user_id', $user->id)->findOrFail($id);

//        foreach ($checkout->items as $item) {
//     dd($item->productVariant); // giờ sẽ hiển thị được bản ghi đúng!
// }



        return view('clients.checkout.show', [
            'categories' => $categoriesForMenu,
            'cartCount' => $cartCount,
            'checkout' => $checkout,
        ]);
    }

    public function cancel($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->findOrFail($id);

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xử lý.');
        }

        $order->status = 'cancelled';
        $order->save();

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }
    public function restore($id)
    {
        $user = Auth::user();
        $order = Order::where('user_id', $user->id)->findOrFail($id);

        if ($order->status !== 'cancelled') {
            return redirect()->back()->with('error', 'Chỉ có thể khôi phục đơn hàng đã hủy.');
        }

        $order->status = 'pending'; // hoặc trạng thái trước đó, nếu bạn lưu lại
        $order->save();

        return redirect()->back()->with('success', 'Đơn hàng đã được khôi phục.');
    }
}
