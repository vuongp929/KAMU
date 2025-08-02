@extends('layouts.client')

@section('content')
<div class="container mt-5">
    <h2>Chi tiết đơn hàng #{{ $checkout->id }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h3>Thông tin sản phẩm</h3>
    @if($checkout->items && count($checkout->items) > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Size</th>
                    <th>Giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checkout->items as $item)
                    <tr>
                        <td>
                            @if(isset($item->productVariant->product->image))
                                <img src="{{ Storage::url($item->productVariant->product->image) }}" width="100px" alt="Ảnh sản phẩm">
                            @else
                                <img src="{{ asset('images/default-product.jpg') }}" width="100px" alt="Mặc định">
                            @endif
                        </td>
                        <td>{{ $item->productVariant->product->name ?? 'Không rõ' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->productVariant->size ?? 'Không rõ' }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Không có sản phẩm trong đơn hàng.</p>
    @endif

    <h3 class="mt-4">Thông tin khách hàng</h3>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Họ và tên</label>
            <p>{{ $checkout->user->name }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Số điện thoại</label>
            <p>{{ $checkout->user->phone }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <p>{{ $checkout->user->email }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Địa chỉ</label>
            <p>{{ $checkout->user->address }}</p>
        </div>
    </div>

    <h4 class="mt-4">Phương thức thanh toán</h4>
    <p>{{ ucfirst($checkout->payment_method) }}</p>

    <h4 class="mt-4">Trạng thái thanh toán</h4>
    <p>{{ $checkout->payment_status }}</p>

    <h4 class="mt-4">Trạng thái đơn hàng</h4>
    <p>{{ $checkout->status }}</p>

    <a href="{{ route('checkout.index') }}" class="btn btn-primary mt-3">Quay lại danh sách đơn hàng</a>
</div>
@endsection
