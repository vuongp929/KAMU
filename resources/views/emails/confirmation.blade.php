<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận đơn hàng</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Cảm ơn bạn đã đặt hàng!</h2>
    <p>Chào {{ $order->name }},</p>
    <p>Chúng tôi đã nhận được đơn hàng #{{ $order->id }} của bạn. Dưới đây là thông tin chi tiết:</p>

    <h3>Thông tin đơn hàng</h3>
    <ul>
        <li><strong>Tổng tiền:</strong> {{ number_format($order->total_price, 0, ',', '.') }} VNĐ</li>
        <li><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</li>
        <li><strong>Địa chỉ giao hàng:</strong> {{ $order->address }}</li>
    </ul>

    <h3>Các sản phẩm đã đặt:</h3>
    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    {{ $item->variant->product->name }} <br>
                    <small>Phân loại: {{ $item->variant->name }}</small>
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất có thể.</p>
    // Dòng 42:
    <p>Cảm ơn bạn đã tin tưởng và mua sắm tại KUMA House!</p>
</body>
</html>