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

        // Lọc theo trạng thái đơn hàng
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->paginate(10);

        return view('admins.orders.index', compact('orders'));
    }

    // Hiển thị chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with([
            'orderItems.variant.product.mainImage',
            'orderItems.variant.product.firstImage',
            'customer'
        ])->findOrFail($id);
        
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
            $newStatus = $request->status;
            $currentStatus = $order->status;
            
            // Kiểm tra logic chuyển trạng thái
            $allowedTransitions = [
                'pending' => ['processing', 'cancelled'],
                'processing' => ['shipping', 'cancelled'],
                'shipping' => ['delivered', 'cancelled'],
                'delivered' => ['completed'],
                'completed' => [], // Không thể chuyển từ completed
                'cancelled' => [], // Không thể chuyển từ cancelled
            ];
            
            if (isset($allowedTransitions[$currentStatus]) && in_array($newStatus, $allowedTransitions[$currentStatus])) {
                // Hoàn trả tồn kho khi chuyển sang trạng thái 'cancelled'
                if ($newStatus === 'cancelled') {
                    // Chỉ hoàn trả nếu đã trừ tồn kho trước đó (COD hoặc đã thanh toán)
                    if ($order->payment_method === 'cod' || $order->payment_status === 'paid') {
                        foreach ($order->items as $orderItem) {
                            $variant = $orderItem->variant;
                            if ($variant) {
                                $variant->increment('stock', $orderItem->quantity);
                            }
                        }
                    }
                }
                
                $order->status = $newStatus;
                
                // Tự động chuyển trạng thái thanh toán thành "paid" khi đơn hàng chuyển sang "completed"
                if ($newStatus === 'completed' && $order->payment_status !== 'paid') {
                    $order->payment_status = 'paid';
                    // Cộng điểm thưởng khi tự động chuyển sang trạng thái đã thanh toán
                    $order->addRewardPointsOnPaymentSuccess();
                }
            } else {
                return redirect()->route('admin.orders.index')->with('error', 'Không thể chuyển từ trạng thái "' . $currentStatus . '" sang "' . $newStatus . '".');
            }
        }

        if ($request->has('payment_status')) {
            $newPaymentStatus = $request->payment_status;
            $currentPaymentStatus = $order->payment_status;
            
            // Kiểm tra logic chuyển trạng thái thanh toán
            $allowedPaymentTransitions = [
                'unpaid' => ['awaiting_payment', 'cod', 'paid'],
                'awaiting_payment' => ['paid', 'unpaid'],
                'cod' => ['paid'],
                'paid' => [], // Không thể chuyển từ paid
            ];
            
            if (isset($allowedPaymentTransitions[$currentPaymentStatus]) && 
                (in_array($newPaymentStatus, $allowedPaymentTransitions[$currentPaymentStatus]) || $currentPaymentStatus === $newPaymentStatus)) {
                
                // Kiểm tra nếu chuyển sang trạng thái 'paid' và chưa từng được thanh toán
                $shouldAddRewardPoints = ($newPaymentStatus === 'paid' && $currentPaymentStatus !== 'paid');
                
                $order->payment_status = $newPaymentStatus;
                
                // Cộng điểm thưởng nếu chuyển sang trạng thái đã thanh toán
                if ($shouldAddRewardPoints) {
                    $order->addRewardPointsOnPaymentSuccess();
                }
            } else {
                return redirect()->route('admin.orders.index')->with('error', 'Không thể chuyển trạng thái thanh toán từ "' . $currentPaymentStatus . '" sang "' . $newPaymentStatus . '".');
            }
        }

        // ✅ Cập nhật lại tổng tiền nếu cần
        $total = $order->orderItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $order->total_price = $total;

        $order->save();

        return redirect()->route('orders.index')->with('success', 'Thông tin đơn hàng đã được cập nhật.');
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
        $order = Order::with('items.variant')->findOrFail($id);

        // Kiểm tra trạng thái đơn hàng - chỉ cho phép hủy khi đang chờ xử lý hoặc đang xử lý
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->route('admin.orders.index')->with('error', 'Đơn hàng không thể hủy vì trạng thái hiện tại.');
        }

        // Hoàn trả số lượng tồn kho nếu đã trừ (COD hoặc đã thanh toán)
        if ($order->payment_method === 'cod' || $order->payment_status === 'paid') {
            foreach ($order->items as $orderItem) {
                $variant = $orderItem->variant;
                if ($variant) {
                    $variant->increment('stock', $orderItem->quantity);
                }
            }
        }

        $order->status = 'cancelled';
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng #' . $order->id . ' đã được hủy thành công!');
    }

    // Hiển thị đơn hàng chờ thanh toán
    public function awaitingPayment(Request $request)
    {
        $query = Order::with('customer')
            ->where('payment_status', 'awaiting_payment')
            ->orderBy('created_at', 'desc');

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

    // Hiển thị đơn hàng chưa thanh toán
    public function unpaidOrders(Request $request)
    {
        $query = Order::with('customer')
            ->whereIn('payment_status', ['unpaid', 'awaiting_payment'])
            ->orderBy('created_at', 'desc');

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

    // Đánh dấu đơn hàng đã thanh toán
    public function markAsPaid($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('error', 'Đơn hàng đã được thanh toán rồi.');
        }
        
        $order->payment_status = 'paid';
        $order->save();
        
        // Cộng điểm thưởng cho người dùng khi admin đánh dấu đã thanh toán
        $order->addRewardPointsOnPaymentSuccess();
        
        return redirect()->back()->with('success', 'Đã đánh dấu đơn hàng #' . $order->id . ' là đã thanh toán và cộng điểm thưởng cho khách hàng.');
    }
}
