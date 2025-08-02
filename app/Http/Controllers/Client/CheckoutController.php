<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserDiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\OrderConfirmationMail;
use App\Models\Discount;


class CheckoutController extends Controller
{
    /**
     * Hiển thị trang checkout với thông tin giỏ hàng.
     */
    // trong app/Http\Controllers\Client\CheckoutController.php
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
    public function index()
    {
        // Kiểm tra user đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        // === BẮT ĐẦU SỬA LỖI ===
        // Thay thế 'thumbnail' bằng 'mainImage' và 'firstImage'
        $cart = Cart::with([
            'items.variant.product.mainImage',
            'items.variant.product.firstImage'
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->first();
        // === KẾT THÚC SỬA LỖI ===

        // Nếu giỏ hàng rỗng, không cho vào checkout, chuyển về trang giỏ hàng
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Lấy các mã giảm giá đang hoạt động
        $discounts = Discount::where('is_active', 1)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->get();

        return view('clients.checkout.index', compact('cart', 'discounts'));
    }

    /**
     * Xử lý logic đặt hàng.
     */
    public function placeOrder(Request $request)
    {
        // 1. Validate
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cod,vnpay,momo',
            'discount_code' => 'nullable|string|max:255',
            'discount_value' => 'nullable|numeric|min:0',
            'final_total' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $cart = Cart::with('items.variant.product')->where('user_id', $user->id)->latest()->first();

        // 2. Kiểm tra lại giỏ hàng
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('home')->with('error', 'Giỏ hàng của bạn đã hết hạn. Vui lòng thử lại.');
        }

        // 3. Xử lý mã giảm giá nếu có
        $discountAmount = 0;
        $discountCode = null;
        $finalTotal = $cart->total_price;

        if (!empty($validated['discount_code'])) {
            $discountResult = $this->applyDiscountCode($validated['discount_code'], $user, $cart->total_price);

            if ($discountResult['success']) {
                $discountAmount = $discountResult['discount_amount'];
                $discountCode = $discountResult['discount_code'];
                $finalTotal = $cart->total_price - $discountAmount;
            } else {
                return back()->with('error', $discountResult['message'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // 4. Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $finalTotal, // Sử dụng giá sau khi áp dụng mã giảm giá
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'unpaid',
                'shipping_address' => json_encode([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                ]),
            ]);
            // 3. Tạo đơn hàng - sử dụng tổng tiền đã được giảm giá
            $finalTotal = $validated['final_total'] ?? $cart->total_price;
            $discountValue = $validated['discount_value'] ?? 0;

            // Kiểm tra và cập nhật mã giảm giá nếu có
            $discountCode = $validated['discount_code'] ?? null;
            $discount = null;
            if ($discountCode) {
                $discount = Discount::where('code', $discountCode)->first();
                if ($discount && $discount->isValid()) {
                    // Tăng số lần sử dụng
                    $discount->incrementUsageCount();
                }
            }

            // 5. Chuyển item và trừ tồn kho
            foreach ($cart->items as $cartItem) {
                $variant = $cartItem->variant;
                if (!$variant || $variant->stock < $cartItem->quantity) {
                    $productName = $variant && $variant->product ? $variant->product->name : 'Sản phẩm không tồn tại';
                    $variantName = $variant ? $variant->name : 'N/A';
                    throw new \Exception("Sản phẩm \"{$productName} - {$variantName}\" không đủ số lượng tồn kho.");
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id ?? $cartItem->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity' => $cartItem->quantity,
                    'price_at_order' => $cartItem->price_at_order, // Sử dụng giá đã lưu trong giỏ hàng
                    'price' => $variant->price ?? 0, // Giá hiện tại của variant
                ]);
                if ($variant) {
                    $variant->decrement('stock', $cartItem->quantity);
                }
            }

            // 6. Xóa giỏ hàng
            $cart->delete();

            // 7. Đánh dấu mã giảm giá đã sử dụng nếu có
            if ($discountCode) {
                $userDiscountCode = UserDiscountCode::where('discount_code', $discountCode)->first();
                if ($userDiscountCode) {
                    $userDiscountCode->markAsUsed();
                }
            }

            DB::commit();

            $order->load('items.variant.product');

            if ($validated['payment_method'] == 'momo') {
                // 8. Thanh toán với Momo
                $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
                $partnerCode = 'MOMOBKUN20180529';
                $accessKey = 'klm05TvNBzhg7h7j';
                $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

                $orderInfo = "Thanh toán qua MoMo";
                $amount = $finalTotal;
                $orderId = time() . "";
                $redirectUrl = route('client.checkout.momo.return', ['order_id' => $order->id]);
                $ipnUrl = "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b"; // URL api để momo gửi dữ liệu json khi thanh toán thành công(có thể tạo ra 1 api để tự cập nhật trạng thái của đơn hàng)
                $extraData = "";

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
                //muốn lấy dữ liệu cho vào db sau khi thanh toán xong thì lấy như thế này $jsonResult['amount']
                return redirect()->to($jsonResult['payUrl']);
            }

            try {
                // 1. Tạo URL xác nhận có chữ ký, hết hạn sau 48 giờ
                $confirmationUrl = URL::temporarySignedRoute(
                    'client.orders.confirm',
                    now()->addHours(48),
                    ['order' => $order->id]
                );

                // 2. Gửi email với Mailable đã được cập nhật
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order, $confirmationUrl));
            } catch (\Exception $e) {
                Log::warning("Gửi email cho đơn hàng #{$order->id} thất bại: " . $e->getMessage());
            }
            // Chuyển hướng người dùng với thông báo chi tiết
            $successMessage = "🎉 Đặt hàng thành công!\n\n";
            $successMessage .= "📋 Mã đơn hàng: #{$order->id}\n";
            $successMessage .= "💰 Tổng tiền: " . number_format($finalTotal, 0, ',', '.') . " VNĐ\n";
            $successMessage .= "📧 Email xác nhận đã được gửi đến: {$validated['email']}\n\n";
            $successMessage .= "📱 Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận đơn hàng!";

            return redirect()->route('home')->with('success', $successMessage);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi đặt hàng: ' . $e->getMessage());

            $errorMessage = "❌ Đặt hàng thất bại!\n\n";
            $errorMessage .= "🔍 Lỗi: " . $e->getMessage() . "\n\n";
            $errorMessage .= "📞 Vui lòng liên hệ hỗ trợ nếu vấn đề vẫn tiếp tục.";

            return back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Validate mã giảm giá (AJAX)
     */
    public function validateDiscount(Request $request)
    {
        $request->validate([
            'discount_code' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $result = $this->applyDiscountCode($request->discount_code, $user, $request->total_amount);

        return response()->json($result);
    }

    /**
     * Áp dụng mã giảm giá
     */
    private function applyDiscountCode($code, $user, $totalAmount)
    {
        // 1. Kiểm tra mã đổi thưởng (UserDiscountCode) trước
        $userDiscountCode = UserDiscountCode::where('discount_code', $code)
            ->where('user_id', $user->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($userDiscountCode) {
            // Kiểm tra giá trị đơn hàng tối thiểu (100,000 VND cho mã đổi thưởng)
            if ($totalAmount < 100000) {
                return [
                    'success' => false,
                    'message' => 'Mã đổi thưởng chỉ áp dụng cho đơn hàng từ 100,000 VND.'
                ];
            }

            // Tính toán số tiền giảm giá từ mã đổi thưởng
            $discountPercentage = $userDiscountCode->discount_percentage * 100; // Chuyển về phần trăm
            $discountAmount = ($totalAmount * $discountPercentage) / 100;

            return [
                'success' => true,
                'discount_amount' => $discountAmount,
                'discount_code' => $code,
                'message' => "Áp dụng mã đổi thưởng thành công! Giảm {$discountPercentage}%"
            ];
        }

        // 2. Kiểm tra mã giảm giá thông thường trong bảng discounts
        $discount = Discount::where('code', $code)
            ->where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->first();

        if (!$discount) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'
            ];
        }

        // Kiểm tra số lần sử dụng
        if ($discount->max_uses && $discount->used_count >= $discount->max_uses) {
            return [
                'success' => false,
                'message' => 'Mã giảm giá đã hết lượt sử dụng.'
            ];
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($totalAmount < $discount->min_order_amount) {
            return [
                'success' => false,
                'message' => 'Đơn hàng phải có giá trị tối thiểu ' . number_format($discount->min_order_amount) . ' VND.'
            ];
        }

        // Kiểm tra xem người dùng đã sử dụng mã này chưa (nếu once_per_order = true)
        if ($discount->once_per_order) {
            $usedDiscount = UserDiscountCode::where('discount_code', $code)
                ->where('user_id', $user->id)
                ->where('is_used', true)
                ->first();

            if ($usedDiscount) {
                return [
                    'success' => false,
                    'message' => 'Bạn đã sử dụng mã giảm giá này trước đó.'
                ];
            }
        }

        // Tính toán số tiền giảm giá
        $discountAmount = 0;
        if ($discount->discount_type === 'percent') {
            $discountAmount = ($totalAmount * $discount->discount) / 100;
        } else {
            $discountAmount = $discount->amount;
        }

        // Cập nhật số lần sử dụng
        $discount->increment('used_count');

        return [
            'success' => true,
            'discount_amount' => $discountAmount,
            'discount_code' => $code,
            'message' => 'Áp dụng mã giảm giá thành công!'
        ];
    }
    public function handleMomoReturn(Request $request)
    {
        // Lấy thông tin đơn hàng từ session hoặc mã orderId
        $orderId = $request->get('order_id'); // được gửi kèm khi redirect
        $order = Order::with('items.variant.product')->where('id', $orderId)->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng sau khi thanh toán.');
        }

        // Kiểm tra trạng thái giao dịch
        if ($request->get('resultCode') == 0) { // 0 nghĩa là thanh toán thành công
            // ✅ Cập nhật trạng thái đơn hàng
            $order->update([
                'payment_status' => 'paid',
            ]);

            // ✅ Gửi email xác nhận
            try {
                $confirmationUrl = URL::temporarySignedRoute(
                    'client.orders.confirm',
                    now()->addHours(48),
                    ['order' => $orderId]
                );

                Mail::to(json_decode($order->shipping_address)->email)
                    ->send(new OrderConfirmationMail($order, $confirmationUrl));
            } catch (\Exception $e) {
                Log::warning("Không thể gửi email xác nhận cho đơn hàng #{$order->id}: " . $e->getMessage());
            }

            // ✅ Thông báo thành công
            $successMessage = "🎉 Đặt hàng thành công!\n\n";
            $successMessage .= "📋 Mã đơn hàng: #{$order->id}\n";
            $successMessage .= "💰 Tổng tiền: " . number_format($order->total_price, 0, ',', '.') . " VNĐ\n";
            $successMessage .= "📧 Email xác nhận đã được gửi đến: " . json_decode($order->shipping_address)->email . "\n\n";
            $successMessage .= "📱 Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận đơn hàng!";

            return redirect()->route('home')->with('success', $successMessage);
        }

        return redirect()->route('home')->with('error', 'Thanh toán không thành công. Vui lòng thử lại.');
    }
}
