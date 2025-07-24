@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="white_card card_height_100 mb_30">
                <div class="white_card_header">
                    <div class="box_header m-0">
                        <div class="main-title">
                            <h3 class="m-0">Danh sách mã giảm giá</h3>
                        </div>
                        <div class="header_more_tool">
                            <a href="{{ route('admins.discounts.create') }}" class="btn btn-primary">Thêm mã giảm giá</a>
                        </div>
                    </div>
                </div>
                <div class="white_card_body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Giá trị giảm (%)</th>
                                    <th>Số tiền giảm (VND)</th>
                                    <th>Đơn hàng tối thiểu</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Số lượng tối đa</th>
                                    <th>Đã sử dụng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($discounts as $discount)
                                <tr>
                                    <td>{{ $discount->code }}</td>
                                    <td>{{ $discount->discount }}%</td>
                                    <td>
                                        @if($discount->discount_type === 'amount' && $discount->amount)
                                            {{ number_format($discount->amount) }} VND
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($discount->min_order_amount) }} VND</td>
                                    <td>{{ $discount->start_at ? (\Carbon\Carbon::parse($discount->start_at)->format('d/m/Y H:i')) : '' }}</td>
                                    <td>{{ $discount->end_at ? (\Carbon\Carbon::parse($discount->end_at)->format('d/m/Y H:i')) : '' }}</td>
                                    <td>{{ $discount->max_uses }}</td>
                                    <td>{{ $discount->used_count }}</td>
                                    <td>
                                        @if($discount->is_active)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Không hoạt động</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admins.discounts.edit', $discount->id) }}" class="btn btn-sm btn-info">Sửa</a>
                                        <form action="{{ route('admins.discounts.destroy', $discount->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $discounts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if (session('success'))
    <div class="alert alert-success animate__animated animate__slideInRight" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(function() {
            const alert = document.querySelector('.alert-success');
            if(alert) {
                alert.classList.remove('animate__slideInRight');
                alert.classList.add('animate__slideOutUp');
                setTimeout(() => alert.remove(), 1000);
            }
        }, 3000);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endif
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('admins/css/bootstrap1.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/style1.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/colors/default.css') }}" id="colorSkinCSS">
@endsection