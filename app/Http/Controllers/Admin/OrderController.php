<?php

namespace App\Http\Controllers\Admin;


use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Hiển thị danh sách đơn hàng
    public function index()
    {
        $orders = Order::with('customer')->orderBy('created_at', 'desc')->paginate(10);
        return view('admins.orders.index', compact('orders'));
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
    public function update(Request $request, $id)
    {
        $order = Order::with('orderItems')->findOrFail($id); // Load cả orderItems để tính toán

        // Cập nhật trạng thái hoặc trạng thái thanh toán
        if ($request->has('status')) {
            $order->status = $request->status;
        }

        if ($request->has('payment_status')) {
            $order->payment_status = $request->payment_status;
        }

        // Tính toán lại total_price dựa trên orderItems
        $total = $order->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $order->total_price = $total;

        // Lưu lại các thay đổi
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Thông tin đơn hàng đã được cập nhật.');
    }
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
}
