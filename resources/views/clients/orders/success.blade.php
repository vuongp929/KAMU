@extends('layouts.client')

@section('content')
    <div class="container mt-5">
        <div class="container py-5 text-center">
            <h2>🎉 Đặt hàng thành công!</h2>
            <p>Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.</p>
            <a href="{{ route('cart') }}" class="btn btn-primary mt-3">Quay lại cửa hàng</a>
        </div>


    </div>
@endsection
