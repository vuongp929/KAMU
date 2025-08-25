@extends('layouts.client')

@section('title', 'Tặng thiệp miễn phí - KUMA House')

@push('styles')
<style>
    .service-page {
        font-family: 'Baloo 2', cursive;
        color: #5d3b80;
        padding: 40px 0;
    }
    .service-header {
        background: linear-gradient(135deg, #a8e6cf, #7fcdcd);
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
    .card-type {
        background: #f0fdf4;
        border: 2px solid #a8e6cf;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        transition: all 0.3s ease;
        text-align: center;
    }
    .card-type:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(168, 230, 207, 0.3);
    }
    .card-icon {
        background: #a8e6cf;
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
    .card-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #5d3b80;
        margin-bottom: 15px;
    }
    .card-description {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #666;
        margin-bottom: 20px;
    }
    .card-features {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }
    .card-features li {
        padding: 8px 0;
        color: #666;
    }
    .card-features li i {
        color: #a8e6cf;
        margin-right: 10px;
        width: 20px;
    }
    .gallery-section {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 40px;
        margin: 40px 0;
    }
    .card-sample {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: all 0.3s ease;
        background: white;
    }
    .card-sample:hover {
        transform: scale(1.05);
    }
    .card-sample img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .card-sample-info {
        padding: 15px;
        text-align: center;
    }
    .how-to-section {
        background: linear-gradient(135deg, #a8e6cf, #7fcdcd);
        color: white;
        border-radius: 15px;
        padding: 40px;
        margin: 40px 0;
    }
    .step-item {
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }
    .step-number {
        background: rgba(255,255,255,0.2);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        margin-right: 20px;
        flex-shrink: 0;
    }
    .step-content h5 {
        margin-bottom: 10px;
        font-weight: 600;
    }
    .benefits-section {
        background: #fff;
        border-radius: 15px;
        padding: 40px;
        margin: 40px 0;
        border: 2px solid #a8e6cf;
    }
    .benefit-item {
        text-align: center;
        padding: 20px;
    }
    .benefit-icon {
        background: #a8e6cf;
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 15px;
    }
    .contact-section {
        background: #f8f9fa;
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
            <h1><i class="fas fa-envelope-open-text me-3"></i>Tặng thiệp miễn phí</h1>
            <p class="lead">Gửi gắm tình cảm qua những tấm thiệp đẹp mắt và ý nghĩa</p>
        </div>
    </div>

    <div class="container">
        <div class="service-content">
            <div class="text-center mb-5">
                <h2 class="mb-3">Các loại thiệp miễn phí</h2>
                <p class="lead">Chúng tôi cung cấp đa dạng các mẫu thiệp cho mọi dịp đặc biệt</p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-type">
                        <div class="card-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3 class="card-title">Thiệp Tình Yêu</h3>
                        <p class="card-description">
                            Dành cho những dịp lãng mạn như Valentine, kỷ niệm, sinh nhật người yêu
                        </p>
                        <ul class="card-features">
                            <li><i class="fas fa-check"></i>Thiết kế lãng mạn, ngọt ngào</li>
                            <li><i class="fas fa-check"></i>Màu sắc hồng, đỏ chủ đạo</li>
                            <li><i class="fas fa-check"></i>Có thể viết lời nhắn riêng</li>
                            <li><i class="fas fa-check"></i>Kích thước 15x10cm</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-type">
                        <div class="card-icon">
                            <i class="fas fa-birthday-cake"></i>
                        </div>
                        <h3 class="card-title">Thiệp Sinh Nhật</h3>
                        <p class="card-description">
                            Chúc mừng sinh nhật với những thiệp đầy màu sắc và vui tươi
                        </p>
                        <ul class="card-features">
                            <li><i class="fas fa-check"></i>Thiết kế vui nhộn, đáng yêu</li>
                            <li><i class="fas fa-check"></i>Nhiều màu sắc tươi sáng</li>
                            <li><i class="fas fa-check"></i>Phù hợp mọi lứa tuổi</li>
                            <li><i class="fas fa-check"></i>Có thể ghi tên tuổi</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-type">
                        <div class="card-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h3 class="card-title">Thiệp Chúc Mừng</h3>
                        <p class="card-description">
                            Dành cho các dịp đặc biệt như tốt nghiệp, thăng chức, khai trương
                        </p>
                        <ul class="card-features">
                            <li><i class="fas fa-check"></i>Thiết kế trang trọng, lịch sự</li>
                            <li><i class="fas fa-check"></i>Màu sắc sang trọng</li>
                            <li><i class="fas fa-check"></i>Phù hợp môi trường công sở</li>
                            <li><i class="fas fa-check"></i>Có thể in logo công ty</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-type">
                        <div class="card-icon">
                            <i class="fas fa-baby"></i>
                        </div>
                        <h3 class="card-title">Thiệp Trẻ Em</h3>
                        <p class="card-description">
                            Thiết kế dành riêng cho trẻ em với hình ảnh hoạt hình đáng yêu
                        </p>
                        <ul class="card-features">
                            <li><i class="fas fa-check"></i>Hình ảnh hoạt hình cute</li>
                            <li><i class="fas fa-check"></i>Màu sắc tươi sáng</li>
                            <li><i class="fas fa-check"></i>An toàn cho trẻ em</li>
                            <li><i class="fas fa-check"></i>Kích thước vừa tay bé</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-type">
                        <div class="card-icon">
                            <i class="fas fa-tree"></i>
                        </div>
                        <h3 class="card-title">Thiệp Lễ Hội</h3>
                        <p class="card-description">
                            Dành cho các dịp lễ như Noel, Tết, Halloween, Trung Thu
                        </p>
                        <ul class="card-features">
                            <li><i class="fas fa-check"></i>Thiết kế theo chủ đề lễ hội</li>
                            <li><i class="fas fa-check"></i>Màu sắc đặc trưng</li>
                            <li><i class="fas fa-check"></i>Cập nhật theo mùa</li>
                            <li><i class="fas fa-check"></i>Phù hợp văn hóa Việt Nam</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-type">
                        <div class="card-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="card-title">Thiệp Cảm Ơn</h3>
                        <p class="card-description">
                            Bày tỏ lòng biết ơn với những thiệp cảm ơn chân thành
                        </p>
                        <ul class="card-features">
                            <li><i class="fas fa-check"></i>Thiết kế thanh lịch</li>
                            <li><i class="fas fa-check"></i>Lời cảm ơn ý nghĩa</li>
                            <li><i class="fas fa-check"></i>Phù hợp mọi đối tượng</li>
                            <li><i class="fas fa-check"></i>Có thể tùy chỉnh nội dung</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="gallery-section">
                <h3 class="text-center mb-4">Mẫu thiệp tham khảo</h3>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card-sample">
                            <img src="https://via.placeholder.com/300x200/a8e6cf/ffffff?text=Thiệp+Tình+Yêu" alt="Thiệp tình yêu">
                            <div class="card-sample-info">
                                <h6>Thiệp Tình Yêu</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card-sample">
                            <img src="https://via.placeholder.com/300x200/7fcdcd/ffffff?text=Thiệp+Sinh+Nhật" alt="Thiệp sinh nhật">
                            <div class="card-sample-info">
                                <h6>Thiệp Sinh Nhật</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card-sample">
                            <img src="https://via.placeholder.com/300x200/a8e6cf/ffffff?text=Thiệp+Chúc+Mừng" alt="Thiệp chúc mừng">
                            <div class="card-sample-info">
                                <h6>Thiệp Chúc Mừng</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card-sample">
                            <img src="https://via.placeholder.com/300x200/7fcdcd/ffffff?text=Thiệp+Trẻ+Em" alt="Thiệp trẻ em">
                            <div class="card-sample-info">
                                <h6>Thiệp Trẻ Em</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="how-to-section">
                <h3 class="text-center mb-4">Cách nhận thiệp miễn phí</h3>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h5>Mua sản phẩm tại KUMA House</h5>
                                <p>Chỉ cần mua bất kỳ sản phẩm nào tại cửa hàng, bạn sẽ được tặng kèm thiệp miễn phí</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h5>Chọn loại thiệp phù hợp</h5>
                                <p>Lựa chọn mẫu thiệp phù hợp với dịp và đối tượng bạn muốn tặng</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h5>Viết lời nhắn cá nhân</h5>
                                <p>Nhân viên sẽ hỗ trợ bạn viết lời nhắn hoặc bạn có thể tự viết</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h5>Nhận thiệp cùng sản phẩm</h5>
                                <p>Thiệp sẽ được đóng gói cùng với sản phẩm của bạn</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="benefits-section">
                <h3 class="text-center mb-4">Lợi ích của dịch vụ</h3>
                <div class="row">
                    <div class="col-md-3">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <h5>Hoàn toàn miễn phí</h5>
                            <p>Không tính thêm bất kỳ chi phí nào</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-palette"></i>
                            </div>
                            <h5>Thiết kế đẹp mắt</h5>
                            <p>Được thiết kế bởi team chuyên nghiệp</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h5>Chất liệu cao cấp</h5>
                            <p>Giấy in chất lượng cao, thân thiện môi trường</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h5>Tăng giá trị món quà</h5>
                            <p>Làm món quà thêm ý nghĩa và đặc biệt</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-section">
                <h4 class="mb-3"><i class="fas fa-info-circle me-2"></i>Thông tin thêm</h4>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Điều kiện áp dụng:</h6>
                        <ul class="text-start">
                            <li>Áp dụng cho tất cả sản phẩm</li>
                            <li>Mỗi đơn hàng được tặng 1 thiệp</li>
                            <li>Có thể chọn nhiều mẫu khác nhau</li>
                            <li>Không áp dụng cho đơn hàng online</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Liên hệ hỗ trợ:</h6>
                        <p><strong>Hotline:</strong> 1900 9999</p>
                        <p><strong>Email:</strong> card@kumahouse.com</p>
                        <p><strong>Địa chỉ:</strong> 123 Nguyễn Văn Cừ, Q.5, TP.HCM</p>
                        <p><strong>Giờ làm việc:</strong> 8:00 - 22:00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection