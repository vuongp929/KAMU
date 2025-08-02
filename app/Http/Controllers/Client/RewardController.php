<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\User;
use App\Models\UserDiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RewardController extends Controller
{
    /**
     * Hiển thị trang quản lý điểm thưởng
     */
    public function index()
    {
        $user = Auth::user();
        $user->load('orders');
        
        return view('clients.points.index', compact('user'));
    }

    /**
     * Quy đổi điểm thành mã giảm giá
     */
    public function exchangePoints(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:100|max:1000',
        ]);

        $user = Auth::user();
        $pointsToExchange = $request->points;

        // Kiểm tra xem người dùng có đủ điểm không
        if ($user->reward_points < $pointsToExchange) {
            return back()->with('error', 'Bạn không đủ điểm để quy đổi.');
        }

        // Hệ thống quy đổi điểm theo mức cố định
        $discountValue = $this->calculateDiscountByPoints($pointsToExchange);
        $discountDecimal = $discountValue / 100; // Chuyển về dạng thập phân
        
        // Tạo mã giảm giá
        $discountCode = 'REWARD_' . strtoupper(Str::random(8));
        
        DB::beginTransaction();
        try {
            // Tạo mã giảm giá trong bảng discounts
            $discount = Discount::create([
                'code' => $discountCode,
                'discount' => $discountValue, // Đã là phần trăm
                'discount_type' => 'percent',
                'amount' => $discountDecimal,
                'start_at' => now(),
                'end_at' => now()->addMonths(3), // Mã giảm giá có hiệu lực trong 3 tháng
                'min_order_amount' => 100000, // Đơn hàng tối thiểu 100,000 VND
                'max_uses' => 1, // Chỉ sử dụng được 1 lần
                'used_count' => 0,
                'is_active' => true,
                'once_per_order' => true,
            ]);

            // Lưu mã giảm giá vào bảng user_discount_codes
            UserDiscountCode::create([
                'user_id' => $user->id,
                'discount_code' => $discountCode,
                'discount_percentage' => $discountDecimal,
                'points_used' => $pointsToExchange,
                'expires_at' => now()->addMonths(3),
                'is_used' => false,
            ]);

            // Trừ điểm thưởng của người dùng
            $user->reward_points -= $pointsToExchange;
            $user->save();

            DB::commit();

            return back()->with('success', "Quy đổi thành công! Mã giảm giá của bạn: {$discountCode} (Giảm {$discountValue}%)");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi quy đổi điểm. Vui lòng thử lại.');
        }
    }

    /**
     * Hiển thị lịch sử điểm thưởng
     */
    public function history()
    {
        $user = Auth::user();
        $orders = $user->orders()->where('payment_status', 'paid')->latest()->paginate(10);
        
        return view('clients.points.history', compact('user', 'orders'));
    }

    /**
     * Hiển thị trang mã đổi thưởng
     */
    public function discountCodes()
    {
        $user = Auth::user();
        $discountCodes = $user->discountCodes()->latest()->paginate(10);
        
        return view('clients.points.discount-codes', compact('user', 'discountCodes'));
    }

    /**
     * Tính toán giảm giá theo số điểm quy đổi
     */
    private function calculateDiscountByPoints($points)
    {
        // Hệ thống mức quy đổi cố định
        if ($points >= 1000) {
            return 10; // 1000 điểm = 10%
        } elseif ($points >= 800) {
            return 9; // 800 điểm = 9%
        } elseif ($points >= 600) {
            return 8; // 600 điểm = 8%
        } elseif ($points >= 400) {
            return 7; // 400 điểm = 7%
        } elseif ($points >= 200) {
            return 6; // 200 điểm = 6%
        } elseif ($points >= 100) {
            return 5; // 100 điểm = 5%
        } else {
            return 0; // Không đủ điểm
        }
    }
} 