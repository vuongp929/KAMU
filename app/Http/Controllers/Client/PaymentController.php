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
        $vnp_Amount = $order->total_price * 100;
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
                
                if ($order && $order->payment_status == 'unpaid') {
                    $order->payment_status = 'paid';
                    $order->status = 'processing';
                    $order->save();
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

        $endpoint = config('services.momo.endpoint');
        $partnerCode = config('services.momo.partner_code');
        $accessKey = config('services.momo.access_key');
        $secretKey = config('services.momo.secret_key');

        if (!$partnerCode || !$accessKey || !$secretKey) {
            Log::error('Lỗi cấu hình Momo: Thiếu thông tin trong file .env hoặc config/services.php');
            return redirect()->route('client.checkout.index')->with('error', 'Hệ thống thanh toán đang được bảo trì.');
        }
        
        $orderInfo = "Thanh toan don hang #" . $order->id;
        $amount = (string)$order->total_price;
        $orderIdMomo = $order->id . '_' . uniqid();
        $redirectUrl = route('payment.momo.return');
        $ipnUrl = route('payment.momo.ipn');
        $requestId = time() . "";
        $requestType = "captureWallet";
        $extraData = "";

        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderIdMomo . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode, 'requestId' => $requestId, 'amount' => $amount, 
            'orderId' => $orderIdMomo, 'orderInfo' => $orderInfo, 'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl, 'lang' => 'vi', 'extraData' => $extraData,
            'requestType' => $requestType, 'signature' => $signature,
        ];

        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        if (isset($jsonResult['payUrl'])) {
            return redirect()->to($jsonResult['payUrl']);
        }
        
        Log::error('Momo payment creation failed.', ['response' => $jsonResult]);
        return redirect()->route('client.checkout.index')->with('error', 'Không thể tạo yêu cầu thanh toán Momo. Vui lòng thử lại.');
    }

    public function returnMomo(Request $request)
    {
        if ($request->query('resultCode') == 0) {
            $orderId = explode('_', $request->query('orderId'))[0];
            $order = Order::find($orderId);
            
            if ($order && $order->payment_status == 'unpaid') {
                $order->payment_status = 'paid';
                $order->status = 'processing';
                $order->save();
                Cart::where('user_id', $order->user_id)->delete();
            }
            return redirect()->route('payment.success');
        }
        return redirect()->route('payment.failed');
    }
    
    public function ipnMomo(Request $request) { /* Logic xử lý IPN của Momo */ }
    
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