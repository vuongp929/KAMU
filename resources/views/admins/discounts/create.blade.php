@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div id="error-alert" class="alert alert-danger animate__animated animate__slideInRight" style="position: relative; z-index: 9999; min-width: 300px;">
            Thêm mã giảm giá bị lỗi
        </div>
        <script>
            setTimeout(function() {
                const alert = document.getElementById('error-alert');
                if(alert) {
                    alert.classList.remove('animate__slideInRight');
                    alert.classList.add('animate__slideOutUp');
                    setTimeout(() => alert.remove(), 1000);
                }
            }, 3000);
        </script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="white_card card_height_100 mb_30">
                <div class="white_card_header">
                    <div class="box_header m-0">
                        <div class="main-title">
                            <h3 class="m-0">Thêm mã giảm giá mới</h3>
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
                    <form action="{{ route('admins.discounts.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="code">Mã giảm giá</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="discount_type">Loại giảm giá</label>
                                    <select class="form-control @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type">
                                        <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                                        <option value="amount" {{ old('discount_type') == 'amount' ? 'selected' : '' }}>Số tiền (VNĐ)</option>
                                    </select>
                                    @error('discount_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3" id="discount_percent_group">
                                    <label for="discount_value">Giá trị giảm (%)</label>
                                    <input type="number" class="form-control @error('discount_value') is-invalid @enderror" id="discount_value" name="discount_value" min="0" max="100" value="{{ old('discount_value') }}">
                                    @error('discount_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3 d-none" id="discount_amount_group">
                                    <label for="amount">Số tiền giảm (VNĐ)</label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" min="0" value="{{ old('amount') }}">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label>&nbsp;</label>
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="once_per_order" name="once_per_order" {{ old('once_per_order') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="once_per_order">Chỉ dùng 1 lần/đơn hàng</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date">Ngày bắt đầu</label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="end_date">Ngày kết thúc</label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
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
                                    <input type="number" class="form-control @error('min_order_amount') is-invalid @enderror" id="min_order_amount" name="min_order_amount" min="0" value="{{ old('min_order_amount') }}">
                                    @error('min_order_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="max_uses">Số lượng tối đa</label>
                                    <input type="number" class="form-control @error('max_uses') is-invalid @enderror" id="max_uses" name="max_uses" min="1" value="{{ old('max_uses') }}">
                                    @error('max_uses')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Kích hoạt</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Thêm mã giảm giá</button>
                        <a href="{{ route('admins.discounts.index') }}" class="btn btn-secondary">Quay lại</a>
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

@section('JS')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var discountType = document.getElementById('discount_type');
    var percentGroup = document.getElementById('discount_percent_group');
    var amountGroup = document.getElementById('discount_amount_group');
    if (discountType && percentGroup && amountGroup) {
        function toggleDiscountFields() {
            var type = discountType.value;
            percentGroup.classList.toggle('d-none', type !== 'percent');
            amountGroup.classList.toggle('d-none', type !== 'amount');
        }
        discountType.addEventListener('change', toggleDiscountFields);
        toggleDiscountFields();
    }
});
</script>
@endsection