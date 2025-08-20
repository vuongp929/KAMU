<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
</head>
<body>
    <h2>Xin chào {{ $user->name }},</h2>
    <p>Cảm ơn bạn đã đặt hàng tại cửa hàng của chúng tôi.</p>
    <p><strong>Mã đơn hàng:</strong> {{ $order->id }}</p>
    <p><strong>Tổng tiền:</strong> {{ number_format($order->final_total ?? $order->total_price, 0, ',', '.') }} VNĐ</p>
    <p>Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất có thể.</p>
    <p>Cảm ơn bạn đã tin tưởng!</p>
</body>
</html>
