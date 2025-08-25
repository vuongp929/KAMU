@extends('layouts.client')

@section('title', 'Giỏ hàng của bạn')

@section('content')
<div class="main">
    <div class="container py-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (isset($cart) && $cart->items->isNotEmpty())
            <div class="row">
                <div class="col-lg-8">
                    <h2>Giỏ hàng của bạn ({{ $cart->total_quantity }} sản phẩm)</h2>
                    <hr>
                    {{-- === MỘT FORM DUY NHẤT BỌC NGOÀI BẢNG === --}}
                    <form action="{{ route('client.cart.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th colspan="2">Sản phẩm</th>
                                        <th>Giá</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Tổng cộng</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->items as $item)
                                        <tr>
                                            <td style="width: 10%;">
                                                <img src="{{ $item->variant->product->thumbnail_url }}" alt="{{ $item->variant->product->name }}" class="img-thumbnail" style="width: 80px;">
                                            </td>
                                            <td>
                                                <a href="{{ route('client.products.show', $item->variant->product) }}"><strong>{{ $item->variant->product->name }}</strong></a>
                                                <br>
                                                <small class="text-muted">Phiên bản: {{ $item->variant->name }}</small>
                                            </td>
                                            <td>{{ number_format($item->price_at_order, 0, ',', '.') }}đ</td>
                                            <td class="text-center" style="width: 20%;">
                                                {{-- Ô input số lượng với name là mảng --}}
                                                <div class="input-group justify-content-center">
                                                    <input type="number" name="quantities[{{ $item->id }}]" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm text-center" style="max-width: 70px;">
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <strong>{{ number_format($item->quantity * $item->price_at_order, 0, ',', '.') }}đ</strong>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('client.cart.remove', $item->id) }}" class="btn btn-sm btn-outline-danger" title="Xóa sản phẩm" onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left"></i> Tiếp tục mua sắm</a>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Cập nhật Giỏ hàng</button>
                        </div>
                    </form>
                </div>
                
                {{-- CỘT TÓM TẮT ĐƠN HÀNG --}}
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title">Tổng cộng giỏ hàng</h4>
                            <hr>
                            <dl class="row">
                                <dt class="col-6">Tổng tiền hàng:</dt>
                                <dd class="col-6 text-end">{{ number_format($cart->total_price, 0, ',', '.') }}đ</dd>

                                <dt class="col-6">Phí vận chuyển:</dt>
                                <dd class="col-6 text-end">Miễn phí</dd>
                            </dl>
                            <hr>
                            <dl class="row h5">
                                <dt class="col-6">Tổng thanh toán:</dt>
                                <dd class="col-6 text-end"><strong>{{ number_format($cart->total_price, 0, ',', '.') }}đ</strong></dd>
                            </dl>
                            <div class="d-grid mt-4">
                                <a href="{{ route('client.checkout.index') }}" class="btn btn-primary">TIẾN HÀNH THANH TOÁN</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- Hiển thị thông báo khi giỏ hàng rỗng --}}
            <div class="text-center py-5">
                <img src="{{ asset('images/empty-cart.svg') }}" alt="Giỏ hàng trống" style="max-width: 200px;" class="mb-3">
                <h4>Giỏ hàng của bạn đang trống!</h4>
                <p class="text-muted">Hãy khám phá thêm các sản phẩm tuyệt vời của chúng tôi.</p>
                <a href="{{ route('home') }}" class="btn btn-primary mt-2">Bắt đầu mua sắm</a>
            </div>
        @endif
    </div>
</div>
@endsection