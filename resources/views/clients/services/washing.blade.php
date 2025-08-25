@extends('layouts.client')

@section('title', 'Dịch vụ giặt gấu - KUMA House')

@push('styles')
<style>
    .service-page {
        font-family: 'Baloo 2', cursive;
        color: #5d3b80;
        padding: 40px 0;
    }
    .service-header {
        background: linear-gradient(135deg, #4ecdc4, #44a08d);
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
    .washing-step {
        background: #f0fdfc;
        border: 2px solid #4ecdc4;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }
    .washing-step:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(78, 205, 196, 0.2);
    }
    .step-icon {
        background: #4ecdc4;
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
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
    .price-card {
        background: linear-gradient(135deg, #4ecdc4, #44a08d);
        color: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        margin: 30px 0;
    }
    .price-item {
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
    }
    .warning-box {
        background: #fff3cd;
        border: 2px solid #ffc107;
        border-radius: 15px;
        padding: 25px;
        margin: 30px 0;
    }
    .contact-section {
        background: #f8f9fa;
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
            <h1><i class="fas fa-soap me-3"></i>Dịch vụ giặt gấu</h1>
            <p class="lead">Chăm sóc và làm sạch gấu bông yêu quý của bạn một cách chuyên nghiệp</p>
        </div>
    </div>

    <div class="container">
        <div class="service-content">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="text-center mb-5">
                        <h2 class="mb-3">Quy trình giặt gấu chuyên nghiệp</h2>
                        <p class="lead">Chúng tôi sử dụng công nghệ và hóa chất an toàn để đảm bảo gấu bông của bạn được làm sạch hoàn hảo</p>
                    </div>

                    <div class="washing-step">
                        <div class="step-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="step-title">Kiểm tra và phân loại</h3>
                        <p class="step-description">
                            Kiểm tra tình trạng gấu bông, phân loại theo chất liệu, màu sắc và mức độ bẩn. 
                            Ghi nhận các vết bẩn đặc biệt cần xử lý riêng.
                        </p>
                    </div>

                    <div class="washing-step">
                        <div class="step-icon">
                            <i class="fas fa-spray-can"></i>
                        </div>
                        <h3 class="step-title">Xử lý vết bẩn cứng đầu</h3>
                        <p class="step-description">
                            Sử dụng dung dịch tẩy vết bẩn chuyên dụng, an toàn cho trẻ em để xử lý các vết bẩn khó tẩy 
                            như mực, thức ăn, hoặc các vết ố lâu ngày.
                        </p>
                    </div>

                    <div class="washing-step">
                        <div class="step-icon">
                            <i class="fas fa-tint"></i>
                        </div>
                        <h3 class="step-title">Giặt sạch với công nghệ hiện đại</h3>
                        <p class="step-description">
                            Sử dụng máy giặt chuyên dụng với chế độ nhẹ nhàng, nước ấm và chất tẩy rửa không gây hại. 
                            Đảm bảo loại bỏ vi khuẩn và mùi hôi mà không làm hỏng chất liệu.
                        </p>
                    </div>

                    <div class="washing-step">
                        <div class="step-icon">
                            <i class="fas fa-wind"></i>
                        </div>
                        <h3 class="step-title">Sấy khô và tạo form</h3>
                        <p class="step-description">
                            Sấy khô ở nhiệt độ phù hợp, vừa đảm bảo khô hoàn toàn vừa giữ được độ mềm mại. 
                            Tạo lại form dáng ban đầu cho gấu bông.
                        </p>
                    </div>

                    <div class="washing-step">
                        <div class="step-icon">
                            <i class="fas fa-sparkles"></i>
                        </div>
                        <h3 class="step-title">Hoàn thiện và đóng gói</h3>
                        <p class="step-description">
                            Chải lông, sắp xếp lại các chi tiết, kiểm tra chất lượng cuối cùng. 
                            Đóng gói cẩn thận trong túi bảo vệ để giao lại cho khách hàng.
                        </p>
                    </div>

                    <div class="price-card">
                        <h4 class="mb-4"><i class="fas fa-tag me-2"></i>Bảng giá dịch vụ</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="price-item">
                                    <h5>Gấu bông nhỏ (dưới 30cm)</h5>
                                    <p class="h4 mb-0">50.000đ</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="price-item">
                                    <h5>Gấu bông trung (30-50cm)</h5>
                                    <p class="h4 mb-0">80.000đ</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="price-item">
                                    <h5>Gấu bông lớn (50-80cm)</h5>
                                    <p class="h4 mb-0">120.000đ</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="price-item">
                                    <h5>Gấu bông siêu lớn (trên 80cm)</h5>
                                    <p class="h4 mb-0">200.000đ</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="warning-box">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Lưu ý quan trọng</h5>
                        <ul class="mb-0">
                            <li>Thời gian giặt: 3-5 ngày làm việc</li>
                            <li>Không nhận giặt gấu bông có pin điện tử hoặc nhạc</li>
                            <li>Gấu bông quá cũ, rách nhiều có thể không đảm bảo chất lượng sau giặt</li>
                            <li>Khách hàng cần thanh toán 100% trước khi nhận dịch vụ</li>
                        </ul>
                    </div>

                    <div class="contact-section">
                        <h4 class="mb-3"><i class="fas fa-phone-alt me-2"></i>Đặt lịch giặt gấu</h4>
                        <p>Liên hệ với chúng tôi để đặt lịch và nhận tư vấn chi tiết</p>
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <p><strong>Hotline:</strong><br>1900 1234</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Email:</strong><br>washing@kumahouse.com</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Địa chỉ:</strong><br>123 Nguyễn Văn Cừ, Q.5, TP.HCM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection