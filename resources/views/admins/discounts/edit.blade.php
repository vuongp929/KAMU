@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="white_card card_height_100 mb_30">
                <div class="white_card_header">
                    <div class="box_header m-0">
                        <div class="main-title">
                            <h3 class="m-0">Chỉnh sửa mã giảm giá</h3>
                        </div>
                    </div>
                </div>
                <div class="white_card_body">
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
                    <form action="{{ route('admin.discounts.update', $discount->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="code">Mã giảm giá</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $discount->code) }}">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="discount_value">Giá trị giảm (%)</label>
                                    <input type="number" class="form-control @error('discount_value') is-invalid @enderror" id="discount_value" name="discount_value" min="0" max="100" value="{{ old('discount_value', $discount->discount) }}">
                                    @error('discount_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date">Ngày bắt đầu</label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $discount->start_at ? date('Y-m-d\TH:i', strtotime($discount->start_at)) : '') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="end_date">Ngày kết thúc</label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $discount->end_at ? date('Y-m-d\TH:i', strtotime($discount->end_at)) : '') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="min_order_amount">Đơn hàng tối thiểu</label>
                                    <input type="number" class="form-control @error('min_order_amount') is-invalid @enderror" id="min_order_amount" name="min_order_amount" min="0" value="{{ old('min_order_amount', $discount->min_order_amount) }}">
                                    @error('min_order_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="max_uses">Số lượng tối đa</label>
                                    <input type="number" class="form-control @error('max_uses') is-invalid @enderror" id="max_uses" name="max_uses" min="1" value="{{ old('max_uses', $discount->max_uses) }}">
                                    @error('max_uses')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Kích hoạt</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('admins/css/bootstrap1.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/style1.css') }}" />
    <link rel="stylesheet" href="{{ asset('admins/css/colors/default.css') }}" id="colorSkinCSS">
@endsection