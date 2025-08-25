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

        // Áp dụng bộ lọc theo trạng thái thanh toán nếu có
        $paymentStatus = $request->query('payment_status');
        if ($paymentStatus) {
            if ($paymentStatus === 'unpaid_orders') {
                // Hiển thị đơn hàng chưa thanh toán (bao gồm COD và awaiting_payment)
                $query->whereIn('payment_status', ['cod', 'awaiting_payment', 'unpaid']);
            } else {
                $query->where('payment_status', $paymentStatus);
            }
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
     * Hiển thị danh sách đơn hàng chưa thanh toán
     */
    public function unpaidOrders(Request $request)
    {
        // Lấy các đơn hàng chưa thanh toán của người dùng hiện tại
        $query = Order::where('user_id', Auth::id())
            ->whereIn('payment_status', ['cod', 'awaiting_payment', 'unpaid']);

        // Tải trước các mối quan hệ cần thiết
        $orders = $query->with([
            'orderItems' => function ($query) {
                $query->with(['productVariant' => function ($subQuery) {
                    $subQuery->withTrashed()->with([
                        'product' => function ($productQuery) {
                            $productQuery->withTrashed()->with(['mainImage', 'firstImage']);
                        }
                    ]);
                }]);
            }
        ])
        ->latest()
        ->paginate(5);

        return view('clients.orders.unpaid', compact('orders'));
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

    /**
     * Hoàn thành đơn hàng và cộng điểm thưởng
     */
    public function complete(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order->status === 'delivered') {
            // Chuyển sang trạng thái completed
            $order->status = 'completed';
            $order->save();

            // Cộng điểm thưởng cho người dùng (chỉ cộng nếu đã thanh toán và chưa cộng điểm)
            if ($order->payment_status === 'paid') {
                $pointsAdded = $order->addRewardPointsOnPaymentSuccess();
                $message = $pointsAdded ? 
                    'Đơn hàng #' . $order->id . ' đã hoàn thành! Bạn nhận được +20 điểm thưởng.' :
                    'Đơn hàng #' . $order->id . ' đã hoàn thành!';
            } else {
                $message = 'Đơn hàng #' . $order->id . ' đã hoàn thành!';
            }

            return redirect()->route('client.orders.index')
                ->with('success', $message);
        }

        return redirect()->route('client.orders.index')
            ->with('error', 'Đơn hàng phải ở trạng thái "Đã giao" để hoàn thành.');
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // Kiểm tra trạng thái đơn hàng - chỉ cho phép hủy khi đang chờ xử lý hoặc đang xử lý
        if (in_array($order->status, ['pending', 'processing'])) {
            // Hoàn trả số lượng tồn kho nếu đã trừ (COD hoặc đã thanh toán)
            if ($order->payment_method === 'cod' || $order->payment_status === 'paid') {
                foreach ($order->items as $orderItem) {
                    $variant = $orderItem->variant;
                    if ($variant) {
                        $variant->increment('stock', $orderItem->quantity);
                    }
                }
            }
            
            // Chuyển sang trạng thái cancelled
            $order->status = 'cancelled';
            $order->save();

            return redirect()->route('client.orders.index')
                ->with('success', 'Đơn hàng #' . $order->id . ' đã được hủy thành công!');
        }

        return redirect()->route('client.orders.index')
            ->with('error', 'Không thể hủy đơn hàng này. Chỉ có thể hủy đơn hàng đang chờ xử lý hoặc đang xử lý.');
    }
}