<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Xử lý thanh toán thành công
     */
    public function paymentSuccess(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::where('id', $orderId)
                     ->where('user_id', Auth::id())
                     ->first();

        if (!$order) {
            return redirect()->route('client.orders.index')
                ->with('error', 'Không tìm thấy đơn hàng.');
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái thanh toán
            $order->payment_status = 'paid';
            $order->save();

            // Cộng điểm thưởng
            $pointsAdded = $order->addRewardPointsOnPaymentSuccess();

            DB::commit();

            if ($pointsAdded) {
                return redirect()->route('client.orders.show', $order)
                    ->with('success', 'Thanh toán thành công! Bạn đã nhận được +20 điểm thưởng.');
            } else {
                return redirect()->route('client.orders.show', $order)
                    ->with('success', 'Thanh toán thành công!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xử lý thanh toán: ' . $e->getMessage());
            return redirect()->route('client.orders.index')
                ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán.');
        }
    }

    /**
     * Xử lý thanh toán thất bại
     */
    public function paymentFailed(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::where('id', $orderId)
                     ->where('user_id', Auth::id())
                     ->first();

        if (!$order) {
            return redirect()->route('client.orders.index')
                ->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Cập nhật trạng thái thanh toán thất bại
        $order->payment_status = 'failed';
        $order->save();

        return redirect()->route('client.orders.show', $order)
            ->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
    }

    /**
     * Xử lý thanh toán COD (Cash on Delivery)
     */
    public function processCodPayment(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // Kiểm tra phương thức thanh toán
        if ($order->payment_method !== 'cod') {
            return redirect()->route('client.orders.show', $order)
                ->with('error', 'Đơn hàng này không sử dụng thanh toán COD.');
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái thanh toán
            $order->payment_status = 'paid';
            $order->save();

            // Cộng điểm thưởng
            $pointsAdded = $order->addRewardPointsOnPaymentSuccess();

            DB::commit();

            if ($pointsAdded) {
                return redirect()->route('client.orders.show', $order)
                    ->with('success', 'Xác nhận thanh toán COD thành công! Bạn đã nhận được +20 điểm thưởng.');
            } else {
                return redirect()->route('client.orders.show', $order)
                    ->with('success', 'Xác nhận thanh toán COD thành công!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xử lý thanh toán COD: ' . $e->getMessage());
            return redirect()->route('client.orders.show', $order)
                ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán.');
        }
    }
} 