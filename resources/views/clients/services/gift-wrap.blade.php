@extends('layouts.client')

@section('title', 'Gói quà siêu đẹp - KUMA House')

@push('styles')
<style>
    .service-page {
        font-family: 'Baloo 2', cursive;
        color: #5d3b80;
        padding: 40px 0;
    }
    .service-header {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
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
    .gift-package {
        background: #fff5f5;
        border: 2px solid #ff6b6b;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        transition: all 0.3s ease;
        text-align: center;
    }
    .gift-package:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(255, 107, 107, 0.2);
    }
    .package-icon {
        background: #ff6b6b;
        color: white;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 20px;
    }
    .package-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #5d3b80;
        margin-bottom: 15px;
    }
    .package-price {
        font-size: 2rem;
        font-weight: 700;
        color: #ff6b6b;
        margin-bottom: 20px;
    }
    .package-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .package-features li {
        padding: 8px 0;
        border-bottom: 1px solid #ffe0e0;
        color: #666;
    }
    .package-features li:last-child {
        border-bottom: none;
    }
    .package-features li i {
        color: #ff6b6b;
        margin-right: 10px;
    }
    .gallery-section {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 40px;
        margin: 40px 0;
    }
    .gallery-item {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .gallery-item:hover {
        transform: scale(1.05);
    }
    .gallery-item img {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }
    .process-step {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-align: center;
    }
    .step-number {
        background: #ff6b6b;
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 auto 15px;
    }
    .contact-section {
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        color: white;
        border-radius: 15px;
        padding: 40px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="service-page">
    <div class="service-header">
        <div class="container">
            <h1><i class="fas fa-gift me-3"></i>Gói quà siêu đẹp</h1>
            <p class="lead">Biến món quà của bạn thành một tác phẩm nghệ thuật đầy ý nghĩa</p>
        </div>
    </div>

    <div class="container">
        <div class="service-content">
            <div class="text-center mb-5">
                <h2 class="mb-3">Các gói dịch vụ gói quà</h2>
                <p class="lead">Chọn gói dịch vụ phù hợp với nhu cầu và ngân sách của bạn</p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="gift-package">
                        <div class="package-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3 class="package-title">Gói Cơ Bản</h3>
                        <div class="package-price">25.000đ</div>
                        <ul class="package-features">
                            <li><i class="fas fa-check"></i>Giấy gói màu sắc đẹp</li>
                            <li><i class="fas fa-check"></i>Ruy băng cơ bản</li>
                            <li><i class="fas fa-check"></i>Thiệp chúc mừng đơn giản</li>
                            <li><i class="fas fa-check"></i>Túi đựng quà</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="gift-package">
                        <div class="package-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="package-title">Gói Cao Cấp</h3>
                        <div class="package-price">50.000đ</div>
                        <ul class="package-features">
                            <li><i class="fas fa-check"></i>Giấy gói cao cấp nhập khẩu</li>
                            <li><i class="fas fa-check"></i>Ruy băng lụa sang trọng</li>
                            <li><i class="fas fa-check"></i>Thiệp chúc mừng thiết kế đẹp</li>
                            <li><i class="fas fa-check"></i>Hộp quà cứng</li>
                            <li><i class="fas fa-check"></i>Sticker trang trí</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="gift-package">
                        <div class="package-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h3 class="package-title">Gói Siêu Đẹp</h3>
                        <div class="package-price">100.000đ</div>
                        <ul class="package-features">
                            <li><i class="fas fa-check"></i>Giấy gói premium độc quyền</li>
                            <li><i class="fas fa-check"></i>Ruy băng kim tuyến cao cấp</li>
                            <li><i class="fas fa-check"></i>Thiệp chúc mừng handmade</li>
                            <li><i class="fas fa-check"></i>Hộp quà gỗ/kim loại</li>
                            <li><i class="fas fa-check"></i>Phụ kiện trang trí đặc biệt</li>
                            <li><i class="fas fa-check"></i>Túi quà branded</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="gallery-section">
                <h3 class="text-center mb-4">Thư viện ảnh gói quà</h3>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="gallery-item">
                            <img src="https://via.placeholder.com/300x250/ff6b6b/ffffff?text=Gói+Quà+1" alt="Gói quà 1">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="gallery-item">
                            <img src="https://via.placeholder.com/300x250/ee5a24/ffffff?text=Gói+Quà+2" alt="Gói quà 2">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="gallery-item">
                            <img src="https://via.placeholder.com/300x250/ff6b6b/ffffff?text=Gói+Quà+3" alt="Gói quà 3">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="gallery-item">
                            <img src="https://via.placeholder.com/300x250/ee5a24/ffffff?text=Gói+Quà+4" alt="Gói quà 4">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h3 class="text-center mb-4">Quy trình gói quà</h3>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="process-step">
                                <div class="step-number">1</div>
                                <h5>Chọn gói</h5>
                                <p>Lựa chọn gói dịch vụ phù hợp</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="process-step">
                                <div class="step-number">2</div>
                                <h5>Thiết kế</h5>
                                <p>Tư vấn và thiết kế theo yêu cầu</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="process-step">
                                <div class="step-number">3</div>
                                <h5>Thực hiện</h5>
                                <p>Gói quà cẩn thận và tỉ mỉ</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="process-step">
                                <div class="step-number">4</div>
                                <h5>Giao hàng</h5>
                                <p>Đóng gói và giao đến tay bạn</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-section">
                <h4 class="mb-3"><i class="fas fa-phone-alt me-2"></i>Đặt dịch vụ gói quà</h4>
                <p class="mb-4">Liên hệ ngay để được tư vấn và báo giá chi tiết</p>
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Hotline:</strong><br>1900 5678</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Email:</strong><br>giftwrap@kumahouse.com</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Thời gian:</strong><br>8:00 - 22:00 hàng ngày</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p><small><i class="fas fa-info-circle me-2"></i>Thời gian thực hiện: 30 phút - 2 giờ tùy theo độ phức tạp</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection