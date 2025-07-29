<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::with('customer')->orderBy('created_at', 'desc');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('id', 'like', "%{$keyword}%")
                    ->orWhereHas('customer', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        $orders = $query->paginate(10);

        return view('admins.orders.index', compact('orders'));
    }

    // Hiển thị chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with(['orderItems.productVariant.product', 'customer'])->findOrFail($id);
        return view('admins.orders.show', compact('order'));
    }

    // Tính lại tổng tiền đơn hàng
    public function updateTotalPrice(Order $order)
    {
        $total = $order->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $order->total_price = $total;
        $order->save();
    }

    // Cập nhật thông tin đơn hàng
    public function update(Request $request, $id)
    {
        $order = Order::with('orderItems')->findOrFail($id);

        // ✅ Ngăn cập nhật nếu đơn đã hoàn thành hoặc bị hủy
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', '❌ Đơn hàng đã "' . ucfirst($order->status) . '" và không thể cập nhật nữa.');
        }

        if ($request->has('status')) {
            $order->status = $request->status;
        }

        if ($request->has('payment_status')) {
            $order->payment_status = $request->payment_status;
        }

        // ✅ Cập nhật lại tổng tiền nếu cần
        $total = $order->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $order->total_price = $total;

        $order->save();

        return redirect()->route('orders.index')->with('success', '✅ Thông tin đơn hàng đã được cập nhật thành công.');
    }



    // Đặt hàng mới
    public function placeOrder(Request $request)
    {
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $request->cartTotal,
            'status' => 'pending',
        ]);

        // Nếu muốn redirect tới trang thanh toán VNPAY QR:
        return redirect()->route('payment.vnpay.qr', ['orderId' => $order->id]);
    }

    // Hủy đơn hàng
    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status != 'Đang chờ xử lý') {
            return redirect()->route('client.orders.index')->with('error', 'Đơn hàng không thể hủy vì trạng thái hiện tại.');
        }

        $order->status = 'Đã hủy';
        $order->save();

        foreach ($order->orderItems as $orderItem) {
            $orderItem->status = 'Đã hủy';
            $orderItem->save();
        }

        return redirect()->route('client.orders.index')->with('success', 'Đơn hàng đã được hủy thành công.');
    }
}
