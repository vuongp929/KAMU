@extends('layouts.admin')

@section('title', 'Dashboard')

@section('CSS')
    <!-- Bạn có thể thêm CSS tùy chỉnh ở đây -->
    <style>
        .card-bg-soft-primary {
            background-color: rgba(54, 162, 235, 0.1);
        }

        .card-bg-soft-info {
            background-color: rgba(23, 162, 184, 0.1);
        }

        .card-bg-soft-success {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .card-title {
            font-size: 1.1rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid mt-4">
        <h3 class="mb-4">📊 Dashboard</h3>

        {{-- Form lọc --}}
        <form method="GET" class="row mb-4">
            <div class="col-md-3">
                <label>Từ ngày</label>
                <input type="date" name="from_date" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-md-3">
                <label>Đến ngày</label>
                <input type="date" name="to_date" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <label>Số lượng hiển thị</label>
                <select name="limit" class="form-select">
                    <option value="5" {{ $limit == 5 ? 'selected' : '' }}>Top 5</option>
                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>Top 10</option>
                </select>
            </div>
            <div class="col-md-2 mt-4">
                <button type="submit" class="btn btn-primary mt-2">Lọc</button>
            </div>
        </form>

        {{-- Doanh thu --}}
        <div class="card mb-4">
            <div class="card-body bg-light">
                <h5 class="card-title">💰 Doanh thu </h5>
                <p class="fs-4 text-success">{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</p>
            </div>

        </div>
        <div class="d-flex justify-content-around">
            {{-- Tổng đơn hàng --}}
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title text-white">📦 Tổng đơn hàng</h5>
                        <p class="fs-4">{{ $totalOrders }}</p>
                    </div>
                </div>
            </div>

            {{-- Tổng sản phẩm đã bán --}}
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">🛒 Tổng sản phẩm bán ra</h5>
                        <p class="fs-4">{{ $totalProductsSold }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top User --}}
        <div class="card mb-4">
            <div class="card-header">👤 Người dùng đặt hàng nhiều nhất</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Số đơn</th>
                            <th>Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topUsers as $user)
                            <tr>
                                <td>{{ $user->customer->name ?? 'Không rõ' }}</td>
                                <td>{{ $user->customer->email ?? 'Không rõ' }}</td>
                                <td>{{ $user->total_orders }}</td>
                                <td>{{ number_format($user->total_spent, 0, ',', '.') }} VNĐ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top sản phẩm --}}
        <div class="card mb-4">
            <div class="card-header">🛒 Sản phẩm bán chạy nhất</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Size</th>
                            <th>Số lượng bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topProducts as $item)
                            <tr>
                                <td>{{ $item->productVariant->product->name ?? 'Không rõ' }}</td>
                                <td>{{ $item->productVariant->size ?? '-' }}</td>
                                <td>{{ $item->total_quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('JS')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js" defer></script> --}}

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
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
    </script> --}}
@endsection
