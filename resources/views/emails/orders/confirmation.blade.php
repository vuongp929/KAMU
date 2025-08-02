<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { color: #5d3b80; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Cảm ơn bạn đã đặt hàng tại Ôm Là Yêu!</h2>
        </div>
        
        <p>Chào <strong>{{ $order->name }}</strong>,</p>
        <p>Chúng tôi đã nhận được đơn hàng <strong>#{{ $order->id }}</strong> của bạn. Đơn hàng sẽ được xử lý và giao đến bạn trong thời gian sớm nhất. Dưới đây là thông tin chi tiết:</p>

        <h3>Thông tin đơn hàng</h3>
        <ul>
            <li><strong>Tổng tiền:</strong> <span style="color: #dc3545; font-weight: bold;">{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</span></li>
            <li><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</li>
            <li><strong>Địa chỉ giao hàng:</strong> {{ $order->address }}</li>
        </ul>

        <h3>Các sản phẩm đã đặt:</h3>
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->variant->product->name ?? 'Sản phẩm không tồn tại' }} <br>
                        <small>Phân loại: {{ $item->variant->name ?? 'N/A' }}</small>
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $confirmationUrl }}" style="background-color: #ea73ac; color: #ffffff; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                Xác nhận đơn hàng
            </a>
        </div>

        <p>Sau khi xác nhận, chúng tôi sẽ tiến hành đóng gói và giao hàng cho bạn trong thời gian sớm nhất.</p>
        <p>Cảm ơn bạn đã tin tưởng và mua sắm tại Ôm Là Yêu!</p>
        
        <div class="footer">
            <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.</p>
            <p>© {{ date('Y') }} Ôm Là Yêu. All rights reserved.</p>
        </div>
    </div>
</body>
</html>