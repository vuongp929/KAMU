<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function test()
    {
        return view('test');
    }
    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // bỏ qua kiểm tra SSL, khi nào có sever https thi bỏ cái này 
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
    public function momo_payment(Request $request)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

        // Lấy tổng tiền từ request
        $finalTotal = $request->input('final_total', 0);
        
        // Nếu có mã giảm giá, lấy thông tin mã giảm giá
        $discountCode = $request->input('discount_code', '');
        $discountValue = $request->input('discount_value', 0);
        
        $orderInfo = "Thanh toán qua MoMo";
        if (!empty($discountCode)) {
            $orderInfo .= " (Mã giảm giá: $discountCode)";
        }
        
        // Chuyển đổi tổng tiền thành chuỗi không có dấu phẩy và không có phần thập phân
        $amount = (string)intval($finalTotal);
        
        $orderId = time() . "";
        $redirectUrl = route('payment.momo.return'); // URL redirect khi thanh toán xong
        $ipnUrl = route('payment.momo.ipn'); // URL api để momo gửi dữ liệu json khi thanh toán thành công
        $extraData = "";

        // Thêm thông tin về mã giảm giá vào extraData nếu có
        if (!empty($discountCode)) {
            $extraData = json_encode([
                'discount_code' => $discountCode,
                'discount_value' => $discountValue
            ]);
        }

        $requestId = time() . "";
        $requestType = "payWithATM";
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json
        
        // Lưu thông tin đơn hàng vào session để có thể truy xuất sau khi thanh toán
        session(['momo_order_info' => [
            'amount' => $amount,
            'order_id' => $orderId,
            'discount_code' => $discountCode,
            'discount_value' => $discountValue,
            'final_total' => $finalTotal
        ]]);
        
        return redirect()->to($jsonResult['payUrl']);
    }
}