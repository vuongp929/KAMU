@extends('layouts.client')

@section('content')
    <div class="container">
        <h2>Giỏ hàng</h2>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- @if (!empty($cart)) --}}
        {{-- @if ($order && $order->orderItems->count()) --}}
        @if ($carts && $carts->isNotEmpty())
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Hình ảnh</th>
                        <th>Size</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng cộng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($carts as $cart)
                        @foreach ($cart->items as $item)
                            <tr>
                                <td>{{ $item->variant->product->name ?? 'Sản phẩm không tồn tại' }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . ($item->variant->product->image ?? 'images/default.png')) }}"
                                        width="100px">
                                </td>
                                <td>{{ $item->size ?? ($item->variant->size ?? 'Không rõ') }}</td>
                                <td>{{ number_format($item->price_at_order ?? ($item->variant->price ?? 0)) }} VND</td>
                                <td>
                                    <form action="{{ route('cart.update') }}" method="POST"
                                        style="display: flex; align-items: center; gap: 8px; flex-wrap: nowrap;">
                                        @csrf
                                        <input type="hidden" name="product_variant_id"
                                            value="{{ $item->product_variant_id }}">

                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm btn-decrease px-2">−</button>

                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                            class="form-control form-control-sm text-center" style="width: 60px;">

                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm btn-increase px-2">＋</button>
                                    </form>
                                </td>

                                <td>{{ number_format($item->quantity * ($item->price_at_order ?? ($item->variant->price ?? 0))) }}
                                    VND</td>
                                <td>
                                    <form action="{{ route('cart.remove') }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                        @csrf
                                        <input type="hidden" name="product_variant_id"
                                            value="{{ $item->product_variant_id }}">
                                        <button type="submit" class="btn btn-sm btn-danger w-100">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <h4>Tổng tất cả đơn hàng: {{ number_format($total) }} VND</h4>
            <a href="{{ route('order.index') }}" class="btn btn-success">Thanh toán</a>
        @else
            <p>Giỏ hàng của bạn đang trống!</p>
        @endif


    </div>
<script>
    document.querySelectorAll('form.auto-submit-form, form[style*="display: flex"]').forEach(form => {
        const decreaseBtn = form.querySelector('.btn-decrease');
        const increaseBtn = form.querySelector('.btn-increase');
        const input = form.querySelector('input[name="quantity"]');

        if (!input) return;

        // Nút giảm
        decreaseBtn?.addEventListener('click', () => {
            let value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
                form.submit();
            }
        });

        // Nút tăng
        increaseBtn?.addEventListener('click', () => {
            let value = parseInt(input.value);
            input.value = value + 1;
            form.submit();
        });

        // Thay đổi thủ công cũng submit luôn
        input?.addEventListener('change', () => {
            if (parseInt(input.value) >= 1) {
                form.submit();
            }
        });
    });
</script>

@endsection
