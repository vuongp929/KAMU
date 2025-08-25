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

        {{-- Doanh thu với biểu đồ --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex align-items-center text-white">
                            <div class="flex-grow-1">
                                <h5 class="card-title text-white mb-1">💰 Tổng Doanh Thu</h5>
                                <h2 class="mb-0 fw-bold animate-number">{{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                                <small class="text-white-50">VNĐ</small>
                            </div>
                            <div class="ms-3">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                    <i class="fas fa-chart-line fa-2x text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">📈 Biểu Đồ Doanh Thu</h6>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="chartType" id="monthly" autocomplete="off" checked>
                                <label class="btn btn-outline-primary btn-sm" for="monthly">
                                    <i class="fas fa-calendar-alt me-1"></i>Tháng
                                </label>
                                <input type="radio" class="btn-check" name="chartType" id="daily" autocomplete="off">
                                <label class="btn btn-outline-primary btn-sm" for="daily">
                                    <i class="fas fa-calendar-day me-1"></i>7 Ngày
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-3">
                        <div class="chart-container" style="position: relative; height: 250px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Thống kê tổng quan --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="d-flex align-items-center text-white">
                            <div class="flex-grow-1">
                                <h5 class="card-title text-white mb-1">📦 Tổng Đơn Hàng</h5>
                                <h2 class="mb-0 fw-bold animate-number">{{ number_format($totalOrders) }}</h2>
                                <small class="text-white-50">Đơn hàng</small>
                            </div>
                            <div class="ms-3">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                    <i class="fas fa-shopping-cart fa-2x text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="d-flex align-items-center text-white">
                            <div class="flex-grow-1">
                                <h5 class="card-title text-white mb-1">🛒 Sản Phẩm Đã Bán</h5>
                                <h2 class="mb-0 fw-bold animate-number">{{ number_format($totalProductsSold) }}</h2>
                                <small class="text-white-50">Sản phẩm</small>
                            </div>
                            <div class="ms-3">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                    <i class="fas fa-box fa-2x text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top User --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">👤 Top Khách Hàng VIP</h5>
                        <small class="text-muted">Khách hàng đặt hàng nhiều nhất</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Khách hàng</th>
                                        <th class="border-0">Đơn hàng</th>
                                        <th class="border-0">Tổng tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topUsers as $index => $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="badge bg-primary rounded-circle me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $user->customer->name ?? 'Không rõ' }}</div>
                                                        <small class="text-muted">{{ $user->customer->email ?? 'Không rõ' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $user->total_orders }} đơn</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">{{ number_format($user->total_spent, 0, ',', '.') }} VNĐ</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-users fa-2x mb-2"></i>
                                                    <p>Chưa có dữ liệu khách hàng</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">🛒 Top Sản Phẩm Hot</h5>
                        <small class="text-muted">Sản phẩm bán chạy nhất</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Sản phẩm</th>
                                        <th class="border-0">Size</th>
                                        <th class="border-0">Đã bán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topProducts as $index => $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="badge bg-warning rounded-circle me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                        {{ $index + 1 }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $item->productVariant->product->name ?? 'Không rõ' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $item->productVariant->size ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">{{ $item->total_quantity }} sp</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                                    <p>Chưa có dữ liệu sản phẩm</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('JS')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    
    <style>
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .stats-card {
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        
        .animate-number {
            animation: countUp 2s ease-out;
        }
        
        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Dữ liệu biểu đồ
            const monthlyData = {
                labels: @json($months),
                datasets: [{
                    label: 'Doanh thu theo tháng',
                    data: @json($monthlyRevenue),
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(102, 126, 234, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            };
            
            const dailyData = {
                labels: @json($days),
                datasets: [{
                    label: 'Doanh thu 7 ngày gần nhất',
                    data: @json($dailyRevenue),
                    backgroundColor: 'rgba(250, 112, 154, 0.1)',
                    borderColor: 'rgba(250, 112, 154, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(250, 112, 154, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            };

            const ctx = document.getElementById('revenueChart');
            if (!ctx) {
                console.error("Không tìm thấy #revenueChart");
                return;
            }

            let currentChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: monthlyData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(102, 126, 234, 1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 12
                                },
                                maxTicksLimit: 6,
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(1) + 'M VNĐ';
                                    } else if (value >= 1000) {
                                        return (value / 1000).toFixed(0) + 'K VNĐ';
                                    }
                                    return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Xử lý chuyển đổi giữa biểu đồ tháng và ngày
            document.querySelectorAll('input[name="chartType"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const newData = this.id === 'monthly' ? monthlyData : dailyData;
                    const newColor = this.id === 'monthly' ? 'rgba(102, 126, 234, 1)' : 'rgba(250, 112, 154, 1)';
                    const newBgColor = this.id === 'monthly' ? 'rgba(102, 126, 234, 0.1)' : 'rgba(250, 112, 154, 0.1)';
                    
                    currentChart.data = newData;
                    currentChart.data.datasets[0].borderColor = newColor;
                    currentChart.data.datasets[0].backgroundColor = newBgColor;
                    currentChart.data.datasets[0].pointBackgroundColor = newColor;
                    currentChart.options.plugins.tooltip.borderColor = newColor;
                    
                    currentChart.update('active');
                });
            });

            // Animation cho số liệu
            const animateNumbers = () => {
                document.querySelectorAll('.animate-number').forEach(el => {
                    el.style.animation = 'countUp 2s ease-out';
                });
            };
            
            // Thêm class animate cho các card
            document.querySelectorAll('.card').forEach(card => {
                card.classList.add('stats-card');
            });
            
            // Trigger animation
            setTimeout(animateNumbers, 500);
        });
    </script>
@endsection
