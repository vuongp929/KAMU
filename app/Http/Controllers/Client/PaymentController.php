<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // ==========================================================
    // === CÁC TRANG KẾT QUẢ CHUNG ==============================
    // ==========================================================

    public function paymentSuccess()
    {
        return view('clients.payment.success')
            ->with('success_message', 'Thanh toán thành công! Đơn hàng của bạn đang được xử lý.');
    }

    public function paymentFailed()
    {
        return view('clients.payment.failed')
            ->with('error_message', 'Thanh toán không thành công. Vui lòng thử lại hoặc chọn phương thức khác.');
    }

    // ==========================================================
    // === PHẦN XỬ LÝ THANH TOÁN VNPAY =========================
    // ==========================================================
        
    public function createVnpay(Request $request)
    {
        $order = Order::findOrFail($request->query('orderId'));
        if ($order->user_id !== Auth::id()) { abort(403); }
        
        // Kiểm tra trạng thái đơn hàng - không cho phép thanh toán nếu đã hủy
        if ($order->status === 'cancelled') {
            return redirect()->route('client.orders.index')->with('error', 'Không thể thanh toán cho đơn hàng đã bị hủy.');
        }

        // Lấy config một cách nhất quán
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');

        // Thêm kiểm tra để đảm bảo config được nạp
        if (!$vnp_TmnCode || !$vnp_HashSecret) {
            Log::error('Lỗi cấu hình VNPay: TmnCode hoặc HashSecret bị thiếu trong file .env hoặc config/services.php');
            return redirect()->route('client.checkout.index')->with('error', 'Hệ thống thanh toán đang được bảo trì. Vui lòng thử lại sau.');
        }
        
        $vnp_Returnurl = route('payment.vnpay.return');
        $vnp_TxnRef = $order->id . '_' . time();
        $vnp_OrderInfo = "Thanh toan don hang #" . $order->id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = ($order->final_total ?? $order->total_price) * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0", "vnp_TmnCode" => $vnp_TmnCode, "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay", "vnp_CreateDate" => date('YmdHis'), "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr, "vnp_Locale" => $vnp_Locale, "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType, "vnp_ReturnUrl" => $vnp_Returnurl, "vnp_TxnRef" => $vnp_TxnRef
        ];
        
        ksort($inputData);
        
        $queryString = "";
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $queryString .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $paymentUrl = $vnp_Url . "?" . $queryString . 'vnp_SecureHash=' . $vnpSecureHash;

        return redirect($paymentUrl);
    }

    public function returnVnpay(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $inputData = $request->all();
        $vnp_SecureHash_received = $inputData['vnp_SecureHash'];
        
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);
        
        ksort($inputData);

        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        $secureHash_generated = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash_generated == $vnp_SecureHash_received) {
            if ($request->vnp_ResponseCode == '00') {
                $orderId = explode('_', $request->vnp_TxnRef)[0];
                $order = Order::find($orderId);
                
                if ($order && $order->payment_status == 'awaiting_payment') {
                    $order->markAsPaid();
                    Cart::where('user_id', $order->user_id)->delete();
                }
                return redirect()->route('payment.success');
            } else {
                return redirect()->route('payment.failed');
            }
        }
        
        Log::warning('VNPay return signature failed.', $request->all());
        return redirect()->route('payment.failed');
    }

    public function ipnVnpay(Request $request) { /* Logic xử lý IPN của VNPay */ }


    // ==========================================================
    // === PHẦN TÍCH HỢP THANH TOÁN MOMO ========================
    // ==========================================================
    
        public function createMomo(Request $request)
    {
        $order = Order::findOrFail($request->query('orderId'));
        if ($order->user_id !== Auth::id()) { abort(403); }
        
        // Kiểm tra trạng thái đơn hàng - không cho phép thanh toán nếu đã hủy
        if ($order->status === 'cancelled') {
            return redirect()->route('client.orders.index')->with('error', 'Không thể thanh toán cho đơn hàng đã bị hủy.');
        }

        $endpoint = config('services.momo.endpoint');
        $partnerCode = config('services.momo.partner_code');
        $accessKey = config('services.momo.access_key');
        $secretKey = config('services.momo.secret_key');

        if (!$partnerCode || !$accessKey || !$secretKey) {
            Log::error('Lỗi cấu hình Momo: Thiếu thông tin.');
            return redirect()->route('client.checkout.index')->with('error', 'Hệ thống thanh toán Momo đang bảo trì.');
        }
        
        $orderInfo = "Thanh toan don hang #" . $order->id;
        $amount = (string)(int)($order->final_total ?? $order->total_price);
        $orderIdMomo = $order->id . '_' . uniqid();
        $requestId = time() . "";
        $requestType = "payWithMethod";
        $extraData = "";
        // Sử dụng URL công khai cho MoMo callback (cần thay đổi khi deploy)
        $baseUrl = env('MOMO_CALLBACK_URL', 'https://your-domain.com');
        $redirectUrl = $baseUrl . '/payment/momo/return';
        $ipnUrl = $baseUrl . '/payment/momo/ipn';
        
        // Chuỗi để tạo chữ ký cho payWithMethod KHÔNG BAO GỒM paymentOptions
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderIdMomo . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        
        // Dữ liệu cuối cùng gửi đi, KHÔNG có paymentOptions
        $data = [
            'partnerCode' => $partnerCode,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderIdMomo,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        Log::info('MoMo payment request data:', $data);
        
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);
        
        Log::info('MoMo API response:', ['raw_response' => $result, 'parsed_response' => $jsonResult]);

        if (isset($jsonResult['payUrl'])) {
            Log::info('MoMo payment URL created successfully:', ['payUrl' => $jsonResult['payUrl']]);
            return redirect()->to($jsonResult['payUrl']);
        }
        
        Log::error('Momo payment creation failed.', [
            'data_sent' => $data, 
            'raw_response' => $result,
            'parsed_response' => $jsonResult,
            'endpoint' => $endpoint
        ]);
        
        $errorMessage = 'Không thể tạo yêu cầu thanh toán Momo.';
        if (isset($jsonResult['message'])) {
            $errorMessage .= ' Lỗi: ' . $jsonResult['message'];
        }
        
        return redirect()->route('client.checkout.index')->with('error', $errorMessage);
    }

    public function returnMomo(Request $request)
    {
        $resultCode = $request->query('resultCode');
        $message = $request->query('message', '');
        
        // Xử lý kết quả thanh toán thành công
        if ($resultCode == 0) {
            $orderId = explode('_', $request->query('orderId'))[0];
            $order = Order::find($orderId);
            
            if ($order && $order->payment_status == 'awaiting_payment') {
                $order->markAsPaid();
                Cart::where('user_id', $order->user_id)->delete();
            }
            return redirect()->route('payment.success');
        }
        
        // Xử lý các mã lỗi cụ thể từ MoMo
        $errorMessage = $this->getMomoErrorMessage($resultCode, $message);
        
        Log::warning('MoMo payment failed', [
            'resultCode' => $resultCode,
            'message' => $message,
            'orderId' => $request->query('orderId')
        ]);
        
        return redirect()->route('payment.failed')->with('error_message', $errorMessage);
    }
    
    /**
     * Lấy thông báo lỗi phù hợp dựa trên mã lỗi MoMo
     */
    private function getMomoErrorMessage($resultCode, $message = '')
    {
        $errorMessages = [
            1000 => 'Giao dịch được khởi tạo, chờ người dùng xác nhận thanh toán.',
            1001 => 'Giao dịch thành công nhưng chưa hoàn tất.',
            1004 => 'Giao dịch bị từ chối do số dư không đủ.',
            1005 => 'Giao dịch bị từ chối do thẻ/tài khoản bị khóa.',
            1006 => 'Giao dịch bị từ chối do vượt quá hạn mức giao dịch.',
            1007 => 'Giao dịch bị từ chối bởi ngân hàng phát hành.',
            2001 => 'Giao dịch thất bại do lỗi hệ thống.',
            2007 => 'Giao dịch bị từ chối do sai thông tin thanh toán.',
            4001 => 'Giao dịch bị từ chối do lỗi định dạng dữ liệu.',
            4100 => 'Giao dịch thất bại do lỗi kết nối.',
            7000 => 'Giao dịch đang được xử lý.',
            7002 => 'Giao dịch bị hủy bởi người dùng.',
            9000 => 'Giao dịch được xác nhận thành công.',
        ];
        
        // Trả về thông báo lỗi cụ thể hoặc thông báo mặc định
        if (isset($errorMessages[$resultCode])) {
            return $errorMessages[$resultCode];
        }
        
        // Nếu có message từ MoMo thì hiển thị
        if (!empty($message)) {
            return "Thanh toán thất bại: {$message}";
        }
        
        return "Thanh toán thất bại với mã lỗi: {$resultCode}. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.";
    }
    
    public function ipnMomo(Request $request) { /* Logic xử lý IPN của Momo */ }
    
    // ==========================================================
    // === PHẦN XỬ LÝ THANH TOÁN COD ============================
    // ==========================================================
    
    /**
     * Xử lý thanh toán COD (Cash on Delivery)
     */
    public function processCodPayment(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }
        
        // Kiểm tra trạng thái đơn hàng
        if ($order->payment_method !== 'cod') {
            return redirect()->back()->with('error', 'Đơn hàng này không phải là thanh toán COD.');
        }
        
        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('error', 'Đơn hàng này đã được thanh toán rồi.');
        }
        
        try {
            // Cập nhật trạng thái thanh toán
            $order->payment_status = 'paid';
            $order->status = 'processing';
            $order->save();
            
            // Cộng điểm thưởng cho người dùng
            $order->addRewardPointsOnPaymentSuccess();
            
            return redirect()->route('client.orders.show', $order)
                ->with('success', 'Xác nhận thanh toán thành công! Bạn đã nhận được +20 điểm thưởng.');
                
        } catch (\Exception $e) {
            Log::error('Lỗi khi xử lý thanh toán COD: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xử lý thanh toán. Vui lòng thử lại.');
        }
    }
    
    // Hàm cURL helper dùng chung
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