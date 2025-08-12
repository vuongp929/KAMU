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

        return view('dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProductsSold',
            'topUsers',
            'topProducts',
            'from',
            'to',
            'limit'
        ));
    }
}
