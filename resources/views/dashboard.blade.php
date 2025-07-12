@extends('layouts.admin')

@section('title', 'Dashboard')

@section('CSS')
<!-- Bạn có thể thêm CSS tùy chỉnh ở đây -->
<style>
    .card-bg-soft-primary { background-color: rgba(54, 162, 235, 0.1); }
    .card-bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
    .card-bg-soft-success { background-color: rgba(40, 167, 69, 0.1); }
    .card-title { font-size: 1.1rem; }
</style>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Dashboard</h1>
            </div>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="row mb-4">
        <!-- Tổng doanh thu -->
        <div class="col-md-4">
            <div class="card card-bg-soft-primary shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-primary">Tổng doanh thu</h5>
                    <p class="display-6 fw-bold text-primary">
                        {{ number_format($revenue, 0, ',', '.') }} VNĐ
                    </p>
                </div>
            </div>
        </div>
        <!-- Tổng số đơn hàng -->
        <div class="col-md-4">
            <div class="card card-bg-soft-info shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-info">Tổng số đơn hàng</h5>
                    <p class="display-6 fw-bold text-info">{{ $ordersCount }}</p>
                </div>
            </div>
        </div>
        <!-- Tổng số sản phẩm -->
        <div class="col-md-4">
            <div class="card card-bg-soft-success shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-success">Tổng số sản phẩm</h5>
                    <p class="display-6 fw-bold text-success">{{ $productsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu theo tháng -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Biểu đồ doanh thu theo tháng</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('JS')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js" defer></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) {
        console.error("Không tìm thấy #revenueChart");
        return;
    }
    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: @json($monthlyRevenue),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' VNĐ';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
