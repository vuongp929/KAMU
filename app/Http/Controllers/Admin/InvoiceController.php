<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
     public function show(Order $order)
    {
        // Chỉ xem hóa đơn cho đơn hàng đã hoàn thành
        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'Chỉ có thể tạo hóa đơn cho đơn hàng đã hoàn thành.');
        }

        $order->load(['orderItems.productVariant.product', 'customer']);

        return view('admins.invoices.show', compact('order'));
    }
}
