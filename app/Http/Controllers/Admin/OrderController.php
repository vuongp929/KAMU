<?php

// Đặt controller vào đúng namespace của Admin
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách tất cả đơn hàng.
     */
    public function index()
    {
        // Tải trước thông tin người dùng ('user') để hiển thị tên khách hàng
        // Sắp xếp theo đơn hàng mới nhất
        $orders = Order::with('user')->latest()->paginate(15);
        
        // Trả về view trong thư mục admins.orders
        return view('admins.orders.index', compact('orders'));
    }

    /**
     * Hiển thị form để tạo mới đơn hàng (ít dùng trong thực tế, nhưng nên có).
     */
    public function create()
    {
        // Thường trang admin không tự tạo đơn hàng, nhưng nếu cần thì đây là nơi xử lý
        return view('admins.orders.create');
    }

    /**
     * Lưu một đơn hàng mới (ít dùng).
     */
    public function store(Request $request)
    {
        // Logic để admin tạo đơn hàng thủ công
        // ...
        return redirect()->route('admin.orders.index')->with('success', 'Tạo đơn hàng thành công.');
    }

    /**
     * Hiển thị chi tiết một đơn hàng cụ thể.
     * Sử dụng Route Model Binding: Laravel tự động tìm Order dựa trên ID trong URL.
     */
    public function show(Order $order)
    {
        // Tải các mối quan hệ cần thiết cho trang chi tiết
        $order->load(['user', 'orderItems.productVariant.product.thumbnail']);
        
        return view('admins.orders.show', compact('order'));
    }

    /**
     * Hiển thị form chỉnh sửa thông tin đơn hàng (ví dụ: trạng thái).
     */
    public function edit(Order $order)
    {
        // Các trạng thái có thể có để hiển thị trong ô select
        $statuses = ['pending', 'processing', 'completed', 'cancelled', 'failed'];
        $paymentStatuses = ['unpaid', 'paid', 'refunded'];

        return view('admins.orders.edit', compact('order', 'statuses', 'paymentStatuses'));
    }

    /**
     * Cập nhật thông tin của một đơn hàng.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled,failed',
            'payment_status' => 'required|string|in:unpaid,paid,refunded',
            // Thêm các validation khác nếu cần (ví dụ: địa chỉ, ghi chú...)
        ]);

        $order->update($validated);

        // TODO: Thêm logic gửi email thông báo cho khách hàng về việc cập nhật đơn hàng.

        return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng #' . $order->id . ' đã được cập nhật.');
    }

    /**
     * Xóa một đơn hàng (có thể là xóa mềm).
     */
    public function destroy(Order $order)
    {
        // Trước khi xóa, có thể cần kiểm tra các điều kiện logic
        // Ví dụ: chỉ cho xóa đơn hàng đã hủy hoặc đã hoàn thành sau 1 năm.
        
        $order->delete(); // Giả sử model Order dùng SoftDeletes

        return redirect()->route('admin.orders.index')->with('success', 'Đã xóa đơn hàng #' . $order->id);
    }
}