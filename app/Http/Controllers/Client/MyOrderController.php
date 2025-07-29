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
        // Bắt đầu câu query với đơn hàng của người dùng hiện tại
        $query = Order::where('user_id', Auth::id());

        // Áp dụng bộ lọc theo trạng thái nếu có
        $status = $request->query('status');
        if ($status) {
            $query->where('status', $status);
        }

        // Tải trước (eager load) tất cả các mối quan hệ cần thiết
        // để tránh lỗi N+1 và xử lý dữ liệu đã bị xóa mềm (soft deleted)
        $orders = $query->with([
            // Tải các orderItems cho mỗi đơn hàng
            'orderItems' => function ($query) {
                // Với mỗi orderItem, tải tiếp biến thể của nó (kể cả đã bị xóa)
                $query->with(['productVariant' => function ($subQuery) {
                    $subQuery->withTrashed()->with([
                        // Với mỗi biến thể, tải sản phẩm cha của nó (kể cả đã bị xóa)
                        'product' => function ($productQuery) {
                            $productQuery->withTrashed()->with(['mainImage', 'firstImage']);
                        }
                    ]);
                }]);
            }
        ])
        ->latest() // Sắp xếp đơn hàng mới nhất lên đầu
        ->paginate(5); // Phân trang, 5 đơn hàng mỗi trang

        return view('clients.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng cụ thể.
     */
    public function show(Order $order)
    {
        // Chính sách bảo mật: Đảm bảo người dùng chỉ có thể xem đơn hàng của chính họ
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }
        
        // Tải trước dữ liệu liên quan cho trang chi tiết
        // Tên mối quan hệ đã được sửa lại
        $order->load([
            'orderItems.productVariant' => function ($query) {
                $query->withTrashed()->with([
                    'product' => function ($productQuery) {
                        $productQuery->withTrashed()->with(['mainImage', 'firstImage', 'images']);
                    }
                ]);
            }
        ]);

        return view('clients.orders.show', compact('order'));
    }
    public function confirm(Order $order)
    {
        // Kiểm tra xem đơn hàng có ở trạng thái 'pending' không
        if ($order->status === 'pending') {
            // Nếu đúng, chuyển trạng thái sang 'processing' (đang xử lý)
            $order->status = 'processing';
            $order->save();

            // Chuyển hướng đến trang danh sách đơn hàng với thông báo thành công
            return redirect()->route('client.orders.index')
                ->with('success', 'Đơn hàng #' . $order->id . ' của bạn đã được xác nhận thành công!');
        }

        // Nếu đơn hàng đã được xử lý trước đó
        return redirect()->route('client.orders.index')
            ->with('info', 'Đơn hàng #' . $order->id . ' đã được xử lý trước đó.');
    }
}