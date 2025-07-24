<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class MyOrderController extends Controller
{
    /**
     * Hiển thị danh sách các đơn hàng của người dùng hiện tại.
     */
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id());

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // === SỬA LẠI DÒNG NÀY ===
        // Tải trước chuỗi quan hệ đầy đủ
        $orders = $query->with([
                            'orderItems.productVariant.product.mainImage',
                            'orderItems.productVariant.product.firstImage'
                        ])
                    ->latest()
                    ->paginate(5);

        return view('clients.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng cụ thể.
     */
    public function show(Order $order)
    {
        // Chính sách bảo mật: Đảm bảo người dùng chỉ có thể xem đơn hàng của chính họ
        if ($order->user_id !== Auth::id()) {
            abort(403); // Lỗi 403 - Forbidden Access
        }
        
        // Tải trước các item của đơn hàng để hiển thị
        $order->load('orderItems.productVariant.product.thumbnail');

        return view('clients.orders.show', compact('order'));
    }
}