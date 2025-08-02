@extends('layouts.client')

@section('css')
  
@endsection
@section('content')
<div class="container py-5">
    <!-- Bài viết -->
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <!-- Tiêu đề và thông tin -->
            <h1 class="mb-3 text-uppercase" style="font-size: 30px; font-family:  cursive;">Giao Hàng Tận Nhà</h1>
            <div class=" text-muted mb-4 small">
                <strong>11:06 | 26/07/2025</strong> • <span class="text-primary">admin</span>
            </div>

            <!-- Hình ảnh -->
           <div class="my-4">
                <img src="https://teddy.vn/wp-content/uploads/2016/10/mua-hang-scaled.jpg"
                    class="img-fluid mx-auto d-block"
                    style="max-width: 100%; height: auto;"
                    alt="Giao hàng tận nhà">
            </div>



            <!-- Nội dung -->
            {{-- <p class="fs-5">Gấu bông sẽ được giao đến tận nhà của bạn. Bạn sẽ nhận hàng trước rồi mới phải thanh toán sau 💖</p>
            <p class="fw-bold">GIAO HÀNG TẬN NHÀ – TẶNG QUÀ TẬN TAY</p> --}}

            <hr>

            <!-- Bình luận -->
            <h5 class="mt-5">Bình luận (1)</h5>
            <div class="border rounded p-3 mb-4 bg-light">
                <strong>Đỗ Minh Tuấn</strong> <span class="text-muted small">– Trả lời</span>
                <p class="mb-0">Tôi cực kì thích con bạnh tuổi đổi cảm xúc</p>
            </div>

            <!-- Form bình luận -->
            <h5 class="mb-3">Viết bình luận</h5>
            <form>
                <div class="mb-3">
                    <label class="form-label">Nội dung bình luận *</label>
                    <textarea class="form-control" rows="4" placeholder="Nhập nội dung..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ tên *</label>
                        <input type="text" class="form-control" placeholder="Nhập họ tên">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số điện thoại *</label>
                        <input type="text" class="form-control" placeholder="Nhập số điện thoại">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Gửi bình luận</button>
            </form>

            <!-- Bài viết tương tự -->
            <div class="mt-5">
                <h5 class="mb-3 text-center">🧸 Bài viết tương tự</h5>
              <div class="row row-cols-2 row-cols-md-2 g-4">
    <div class="col">
        <a href="#" class="text-decoration-none text-dark d-block border rounded p-3 h-100 shadow-sm">
            <strong>Chính Sách Khách Hàng</strong><br>
            <small>CHÍNH SÁCH ĐỐI VỚI KHÁCH HÀNG CỦA TEDDY.VN</small>
        </a>
    </div>
    <div class="col">
        <a href="#" class="text-decoration-none text-dark d-block border rounded p-3 h-100 shadow-sm">
            <strong>Dịch Vụ Giặt Gấu</strong><br>
            <small>Hướng dẫn giặt tại nhà – Gấu sạch như mới</small>
        </a>
    </div>
    <div class="col">
        <a href="#" class="text-decoration-none text-dark d-block border rounded p-3 h-100 shadow-sm">
            <strong>Giao Hàng Nội Thành 30p – 60p</strong>
        </a>
    </div>
    <div class="col">
        <a href="#" class="text-decoration-none text-dark d-block border rounded p-3 h-100 shadow-sm">
            <strong>Giao Hàng Đi Các Tỉnh 2 – 4 Ngày</strong>
        </a>
    </div>
    <div class="col">
        <a href="#" class="text-decoration-none text-dark d-block border rounded p-3 h-100 shadow-sm">
            <strong>Tặng Thiệp Miễn Phí</strong>
        </a>
    </div>
    <div class="col">
        <a href="#" class="text-decoration-none text-dark d-block border rounded p-3 h-100 shadow-sm">
            <strong>Gói Quà Siêu Đẹp</strong>
        </a>
    </div>
</div>

            </div>

        </div>
          </div>
</div>
@endsection
