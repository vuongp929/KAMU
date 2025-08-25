@extends('layouts.client')

@section('title', 'Hướng dẫn mua hàng - KUMA House')

@push('styles')
<style>
    .service-page {
        font-family: 'Baloo 2', cursive;
        color: #5d3b80;
        padding: 40px 0;
    }
    .service-header {
        background: linear-gradient(135deg, #ea73ac, #fde2f3);
        color: white;
        padding: 60px 0;
        text-align: center;
        margin-bottom: 40px;
    }
    .service-header h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    .service-content {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    .step-card {
        background: #fff0f5;
        border: 2px solid #fde2f3;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }
    .step-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(234, 115, 172, 0.2);
    }
    .step-number {
        background: #ea73ac;
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .step-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #5d3b80;
        margin-bottom: 15px;
    }
    .step-description {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #666;
    }
    .highlight-box {
        background: linear-gradient(135deg, #ea73ac, #ff9ec7);
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin: 30px 0;
        text-align: center;
    }
    .contact-info {
        background: #fffafc;
        border: 2px solid #fde2f3;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="service-page">
    <div class="service-header">
        <div class="container">
            <h1><i class="fas fa-shopping-cart me-3"></i>Hướng dẫn mua hàng</h1>
            <p class="lead">Quy trình mua hàng đơn giản và thuận tiện tại KUMA House</p>
        </div>
    </div>

    <div class="container">
        <div class="service-content">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3 class="step-title">Chọn sản phẩm yêu thích</h3>
                        <p class="step-description">
                            Duyệt qua bộ sưu tập gấu bông đa dạng của chúng tôi. Sử dụng bộ lọc để tìm kiếm theo danh mục, 
                            kích thước, hoặc giá cả. Nhấn vào sản phẩm để xem chi tiết và hình ảnh.
                        </p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3 class="step-title">Thêm vào giỏ hàng</h3>
                        <p class="step-description">
                            Chọn kích thước và số lượng mong muốn, sau đó nhấn "Thêm vào giỏ hàng". 
                            Bạn có thể tiếp tục mua sắm hoặc xem giỏ hàng để kiểm tra đơn hàng.
                        </p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3 class="step-title">Đăng nhập hoặc đăng ký</h3>
                        <p class="step-description">
                            Để tiến hành thanh toán, bạn cần đăng nhập vào tài khoản. Nếu chưa có tài khoản, 
                            hãy đăng ký nhanh chóng với email hoặc số điện thoại.
                        </p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3 class="step-title">Nhập thông tin giao hàng</h3>
                        <p class="step-description">
                            Điền đầy đủ thông tin người nhận, địa chỉ giao hàng và số điện thoại liên hệ. 
                            Chọn phương thức giao hàng phù hợp với nhu cầu của bạn.
                        </p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">5</div>
                        <h3 class="step-title">Chọn phương thức thanh toán</h3>
                        <p class="step-description">
                            Chúng tôi hỗ trợ nhiều phương thức thanh toán: COD (thanh toán khi nhận hàng), 
                            chuyển khoản ngân hàng, ví điện tử MoMo, VNPay.
                        </p>
                    </div>

                    <div class="step-card">
                        <div class="step-number">6</div>
                        <h3 class="step-title">Xác nhận đơn hàng</h3>
                        <p class="step-description">
                            Kiểm tra lại thông tin đơn hàng và nhấn "Đặt hàng". Bạn sẽ nhận được email xác nhận 
                            và có thể theo dõi trạng thái đơn hàng trong tài khoản.
                        </p>
                    </div>

                    <div class="highlight-box">
                        <h4><i class="fas fa-gift me-2"></i>Ưu đãi đặc biệt</h4>
                        <p class="mb-0">Miễn phí giao hàng cho đơn hàng từ 500.000đ trong nội thành TP.HCM</p>
                    </div>

                    <div class="contact-info">
                        <h4 class="mb-3"><i class="fas fa-headset me-2"></i>Cần hỗ trợ?</h4>
                        <p><strong>Hotline:</strong> 1900 1234</p>
                        <p><strong>Email:</strong> support@kumahouse.com</p>
                        <p class="mb-0"><strong>Thời gian hỗ trợ:</strong> 8:00 - 22:00 (Thứ 2 - Chủ nhật)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection