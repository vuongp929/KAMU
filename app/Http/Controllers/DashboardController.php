<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from_date;
        $to = $request->to_date;
        $limit = $request->input('limit', 5);

        // Query cơ bản cho đơn hàng completed
        $completedQuery = Order::where('status', 'completed');
        $orderQuery = Order::query();
        $orderItemQuery = OrderItem::query();

        if ($from && $to) {
            $completedQuery->whereBetween('created_at', [$from, $to]);
            $orderQuery->whereBetween('created_at', [$from, $to]);
            $orderItemQuery->whereHas('order', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            });
        }

        // 1. Tổng doanh thu
        $totalRevenue = $completedQuery->sum('total_price');

        // 2. Tổng đơn hàng (mọi trạng thái)
        $totalOrders = $orderQuery->count();

        // 3. Tổng sản phẩm bán ra
        $totalProductsSold = $orderItemQuery
            ->whereHas('order', function ($q) {
                $q->where('status', 'completed');
            })
            ->sum('quantity');

        // 4. Top user
        $topUsers = $completedQuery
            ->select('user_id', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_price) as total_spent'))
            ->groupBy('user_id')
            ->orderByDesc('total_orders')
            ->with('customer')
            ->take($limit)
            ->get();

        // 5. Top sản phẩm bán chạy
        $topProducts = $orderItemQuery
            ->whereHas('order', function ($q) {
                $q->where('status', 'completed');
            })
            ->select('product_variant_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_variant_id')
            ->orderByDesc('total_quantity')
            ->with('productVariant.product')
            ->take($limit)
            ->get();

        // 6. Dữ liệu biểu đồ doanh thu theo tháng (12 tháng gần nhất)
        $monthlyRevenue = [];
        $months = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->startOfMonth()->toDateString();
            $monthEnd = $month->endOfMonth()->toDateString();
            
            $revenue = Order::where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_price');
                
            $months[] = $month->format('m/Y');
            $monthlyRevenue[] = $revenue;
        }

        // 7. Dữ liệu biểu đồ doanh thu theo ngày (7 ngày gần nhất)
        $dailyRevenue = [];
        $days = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayStart = $day->startOfDay();
            $dayEnd = $day->endOfDay();
            
            $revenue = Order::where('status', 'completed')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->sum('total_price');
                
            $days[] = $day->format('d/m');
            $dailyRevenue[] = $revenue;
        }

        return view('dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProductsSold',
            'topUsers',
            'topProducts',
            'from',
            'to',
            'limit',
            'monthlyRevenue',
            'months',
            'dailyRevenue',
            'days'
        ));
    }
}
