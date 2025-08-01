<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Xử lý thanh toán thành công chung.
     * Trang này sẽ được gọi đến sau khi thanh toán online thành công.
     */
    public function paymentSuccess()
    {
        // Có thể lấy đơn hàng cuối cùng hoặc dựa vào session
        $order = Order::where('user_id', Auth::id())->latest()->first();
        
        return view('clients.payment.success', compact('order'))
            ->with('success_message', 'Thanh toán thành công! Đơn hàng của bạn đang được xử lý.');
    }

    /**
     * Xử lý thanh toán thất bại chung.
     */
    public function paymentFailed()
    {
        return view('clients.payment.failed')
            ->with('error_message', 'Thanh toán không thành công. Vui lòng thử lại hoặc chọn phương thức khác.');
    }

    /**
     * Xử lý thanh toán COD (Cash on Delivery).
     * Phương thức này có thể được gọi từ CheckoutController sau khi tạo đơn hàng COD.
     */
    public function processCodPayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($order->payment_method !== 'cod') {
            return back()->with('error', 'Phương thức thanh toán không hợp lệ.');
        }

        // Với COD, trạng thái có thể là "processing" và payment_status là "unpaid"
        // Việc chuyển sang 'paid' sẽ do admin thực hiện khi nhận được tiền.
        // Tuy nhiên, nếu bạn muốn ghi nhận là 'paid' ngay, logic dưới đây là đúng.

        DB::beginTransaction();
        try {
            $order->payment_status = 'cod_verified'; // Một trạng thái rõ ràng hơn
            $order->save();
            
            // Xóa giỏ hàng
            Cart::where('user_id', Auth::id())->delete();
            
            DB::commit();

            return redirect()->route('client.orders.show', $order)
                ->with('success', 'Đặt hàng COD thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xử lý COD: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra.');
        }
    }


    // ==========================================================
    // === BẮT ĐẦU PHẦN TÍCH HỢP THANH TOÁN MOMO ================
    // ==========================================================
    
    /**
     * Tạo yêu cầu thanh toán đến Momo và chuyển hướng người dùng.
     */
public function createMomo(Request $request)
{
    $order = Order::findOrFail($request->query('orderId'));
    if ($order->user_id !== Auth::id()) { abort(403); }

    // --- DỮ LIỆU GỬI ĐI ---
    $endpoint = config('services.momo.endpoint');
    $partnerCode = config('services.momo.partner_code');
    $accessKey = config('services.momo.access_key');
    $secretKey = config('services.momo.secret_key');
    
    $orderInfo = "Thanh toan don hang #" . $order->id;
    $amount = (string)$order->total_price;
    $orderIdMomo = $order->id . '_' . uniqid(); // Dùng uniqid() để chắc chắn duy nhất
    $redirectUrl = route('payment.momo.return');
    $ipnUrl = route('payment.momo.ipn');
    $requestId = time() . "";
    $requestType = "captureWallet";
    $extraData = "";

    // --- TẠO CHỮ KÝ ---
    $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderIdMomo . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
    $signature = hash_hmac("sha256", $rawHash, $secretKey);

    $data = [
        'partnerCode' => $partnerCode, 'requestId' => $requestId,
        'amount' => $amount, 'orderId' => $orderIdMomo,
        'orderInfo' => $orderInfo, 'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl, 'lang' => 'vi',
        'extraData' => $extraData, 'requestType' => $requestType,
        'signature' => $signature,
    ];

    // --- GỬI REQUEST VÀ DEBUG KẾT QUẢ ---
    $result = $this->execPostRequest($endpoint, json_encode($data));
    $jsonResult = json_decode($result, true);

    // Dừng chương trình và hiển thị tất cả thông tin để debug
    // dd([
    //     'endpoint' => $endpoint,
    //     'data_sent' => $data,
    //     'raw_hash_string' => $rawHash,
    //     'momo_response' => $jsonResult // <-- ĐÂY LÀ PHẦN QUAN TRỌNG NHẤT
    // ]);

    // Code bên dưới sẽ không chạy khi có dd()
    if (isset($jsonResult['payUrl'])) {
        return redirect()->to($jsonResult['payUrl']);
    }

    return redirect()->route('client.checkout.index')->with('error', 'Không thể tạo yêu cầu thanh toán Momo.');
}

    /**
     * Xử lý khi người dùng được chuyển hướng về từ Momo sau khi thanh toán.
     */
    public function returnMomo(Request $request)
    {
        // TODO: Cần có logic xác thực chữ ký của Momo trả về để đảm bảo an toàn
        // $isValidSignature = $this->verifyMomoSignature($request->all());
        
        if ($request->query('resultCode') == 0) { // resultCode = 0 là giao dịch thành công
            $orderId = explode('_', $request->query('orderId'))[0];
            $order = Order::find($orderId);
            
            if ($order && $order->payment_status == 'unpaid') {
                $order->payment_status = 'paid';
                $order->status = 'processing';
                $order->save();
                
                // Xóa giỏ hàng của người dùng
                Cart::where('user_id', $order->user_id)->delete();
                
                // TODO: Gửi email, cộng điểm thưởng...
            }
            
            return redirect()->route('payment.success');
        }
        
        return redirect()->route('payment.failed');
    }
    
    /**
     * Xử lý IPN từ Momo (server-to-server).
     */
    public function ipnMomo(Request $request)
    {
        // Đây là nơi an toàn nhất để cập nhật trạng thái đơn hàng vì nó được Momo gọi trực tiếp
        // Logic xác thực chữ ký và cập nhật DB sẽ được đặt ở đây
    }
    
    // Hàm cURL helper
    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}