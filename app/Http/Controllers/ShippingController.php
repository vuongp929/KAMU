<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    private $apiUrl;
    private $token;

    public function __construct()
    {
        $this->apiUrl = config('services.ghn.api_url');
        $this->token = config('services.ghn.token');
    }

    // Hàm helper để gọi API GHN
    private function callGhnApi($endpoint, $params = [], $method = 'GET')
    {
        // $token = config('services.ghn.token');
        // $shopId = config('services.ghn.shop_id');
        // $apiUrl = config('services.ghn.api_url');
        
        // // Dừng lại và kiểm tra
        // dd([
        //     'TOKEN_USED' => $token,
        //     'SHOP_ID_USED' => $shopId,
        //     'API_URL_USED' => $apiUrl,
        //     'FULL_REQUEST_URL' => $apiUrl . $endpoint
        // ]);
         $response = Http::withHeaders([
            'token' => $this->token,
            'ShopId' => config('services.ghn.shop_id') // <-- THÊM DÒNG NÀY
        ]);

        // Gửi request
        if (strtoupper($method) === 'GET') {
            $response = $response->get($this->apiUrl . $endpoint, $params);
        } else {
            $response = $response->post($this->apiUrl . $endpoint, $params);
        }
        // === KẾT THÚC SỬA LỖI ===

        // Thêm log để debug request và response
        Log::info('GHN API Call:', [
            'endpoint' => $endpoint,
            'params' => $params,
            'response_status' => $response->status(),
            'response_body' => $response->json()
        ]);
        
        return $response->json();
    }

    public function getProvinces()
    {
        $data = $this->callGhnApi('master-data/province');
        return response()->json($data);
    }

    public function getDistricts(Request $request)
    {
        $request->validate(['province_id' => 'required|integer']);
        $data = $this->callGhnApi('master-data/district', ['province_id' => $request->province_id]);
        return response()->json($data);
    }

    public function getWards(Request $request)
    {
        $request->validate(['district_id' => 'required|integer']);
        $data = $this->callGhnApi('master-data/ward', ['district_id' => $request->district_id]);
        return response()->json($data);
    }

    public function calculateFee(Request $request)
    {
        $validated = $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
        ]);

        $shopId = config('services.ghn.shop_id');
        if (!$shopId) {
            return response()->json(['code' => 500, 'message' => 'Shop ID chưa được cấu hình.'], 500);
        }

        // --- DỮ LIỆU KHO HÀNG CỦA BẠN (ĐÃ ĐƯỢC XÁC THỰC) ---
        // ID Quận Nam Từ Liêm từ kết quả debug
        $from_district_id = 3440; 
        // TODO: Bạn cần tìm WardCode cho "Mễ Trì Thượng" hoặc "Mễ Trì"
        // Tạm thời để một WardCode của Nam Từ Liêm, ví dụ Phường Mỹ Đình 1 là "1A0101"
        $from_ward_code = "1A0114"; // <-- WardCode của Mễ Trì

        // 1. Lấy danh sách dịch vụ khả dụng
        $availableServices = $this->callGhnApi('v2/shipping-order/available-services', [
            'shop_id' => (int)$shopId,
            'from_district' => $from_district_id,
            'to_district' => (int)$validated['to_district_id']
        ]);

        if (empty($availableServices['data'])) {
            Log::warning('GHN: No available services', ['from' => $from_district_id, 'to' => $validated['to_district_id']]);
            return response()->json(['code' => 400, 'message' => 'Không có dịch vụ vận chuyển cho tuyến đường này.'], 400);
        }
        
        $service_id = $availableServices['data'][0]['service_id'];

        // 2. Tính phí với dịch vụ đã tìm được
        $params = [
            'from_district_id' => $from_district_id,
            'from_ward_code' => $from_ward_code,
            'to_district_id' => (int)$validated['to_district_id'],
            'to_ward_code' => $validated['to_ward_code'],
            'service_id' => $service_id,
            'weight' => 500, // gram
            'height' => 20,
            'length' => 20,
            'width' => 10,
        ];
        
        // API tính phí sử dụng GET
        $data = $this->callGhnApi('v2/shipping-order/fee', $params, 'GET'); 
        
        return response()->json($data);
    }
}