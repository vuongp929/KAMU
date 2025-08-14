<style>
.pre-footer {
    background: linear-gradient(135deg, #fde2f3 0%, #ea73ac 50%, #5d3b80 100%);
    padding: 50px 0;
    color: #5d3b80;
    font-family: 'VL BoosterNextFYBlack', sans-serif;
    box-shadow: 0 -10px 30px rgba(93, 59, 128, 0.2);
    position: relative;
    overflow: hidden;
}

.pre-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

.pre-footer h5 {
    font-size: 1.8rem;
    font-weight: bold;
    color: #5d3b80;
    margin-bottom: 20px;
    text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.5);
    position: relative;
}

.pre-footer h5::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #ea73ac, #5d3b80);
    border-radius: 2px;
}

.store-info, .bank-info, .teddy-info {
    margin-bottom: 30px;
    padding: 25px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(234, 115, 172, 0.3);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.store-info:hover, .bank-info:hover, .teddy-info:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(234, 115, 172, 0.3);
    background: rgba(255, 255, 255, 0.9);
}

.social-icons {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.social-icons a {
    display: inline-block;
    width: 40px;
    height: 40px;
    background: rgba(234, 115, 172, 0.2);
    border-radius: 50%;
    padding: 8px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.social-icons a:hover {
    transform: scale(1.2) rotate(360deg);
    background: rgba(234, 115, 172, 0.4);
    border-color: #ea73ac;
    box-shadow: 0 5px 15px rgba(234, 115, 172, 0.4);
}

.social-icons img {
    width: 100%;
    height: auto;
    filter: brightness(0) invert(1);
    transition: all 0.3s ease;
}

.social-icons a:hover img {
    filter: brightness(1) invert(0);
}

ul {
    list-style: none;
    padding: 0;
}

ul li {
    margin-bottom: 12px;
    font-size: 1rem;
    color: #5d3b80;
    padding: 8px 0;
    border-bottom: 1px solid rgba(93, 59, 128, 0.2);
    transition: all 0.3s ease;
}

ul li:hover {
    padding-left: 10px;
    color: #ea73ac;
    border-bottom-color: #ea73ac;
}

.pre-footer p {
    margin-bottom: 10px;
    font-size: 1.1rem;
    color: #5d3b80;
    line-height: 1.6;
    text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.3);
}

@media (max-width: 768px) {
    .pre-footer {
        padding: 30px 0;
    }
    
    .store-info, .bank-info, .teddy-info {
        margin-bottom: 20px;
        padding: 20px;
    }
    
    .pre-footer h5 {
        font-size: 1.5rem;
    }
}
</style>

<footer class="pre-footer">
  <div class="container">
      <div class="row">
          <!-- Thông tin cửa hàng -->
          <div class="col-md-4 store-info">
              <h5>Teddy.vn</h5>
              <p>388 Xã Đàn, Đống Đa, Hà Nội</p>
              <p>096.5555.346 - 096.2222.346</p>
              <p>Hãy kết nối với chúng mình!</p>
              <div class="social-icons">
                  <a href="#" class="icon"><img src="https://teddy.vn/wp-content/uploads/2022/08/Icon-Facebook.png" alt="Facebook"></a>
                  <a href="#" class="icon"><img src="https://teddy.vn/wp-content/uploads/2022/08/Icon-Location.png" alt="Location"></a>
                  <a href="#" class="icon"><img src="https://teddy.vn/wp-content/uploads/2022/08/Icon-Instagram.png" alt="Instagram"></a>
                  <a href="#" class="icon"><img src="https://teddy.vn/wp-content/uploads/2022/08/Icon-Youtube.png" alt="Youtube"></a>
                  <a href="#" class="icon"><img src="https://teddy.vn/wp-content/uploads/2024/07/Icon-Tiktok.png" alt="Tiktok"></a>
              </div>
          </div>
          <!-- Thông tin thanh toán -->
          <div class="col-md-4 bank-info">
              <h5>Thông Tin Thanh Toán</h5>
              <p>Số tài khoản ngân hàng: 0972926888</p>
              <p>MB Bank</p>
              <p>Chủ TK: Nguyễn Thành Trung</p>
          </div>

          <!-- Dịch vụ Teddy -->
          <div class="col-md-4 teddy-info">
              <h5>6 Lý Do Chọn Teddy.vn</h5>
              <ul>
                  <li>🎁 Gói Quà - Nén Nhỏ Gấu - Tặng Thiệp Miễn Phí</li>
                  <li>🚚 Giao Hàng Nội Thành Siêu Tốc - Giao Đúng Giờ & Tận Tay</li>
                  <li>📦 Giao Hàng Toàn Quốc 2 - 5 Ngày - Nhận Hàng Mới Phải Trả Tiền</li>
                  <li>🧵 Bảo Hành Đường Chỉ Vĩnh Viễn - Bảo Hành Bông Gấu 1 Năm</li>
                  <li>🧼 Dịch Vụ Giặt Gấu & Vệ Sinh Gấu Tại Nhà Giá Rẻ</li>
                  <li>📍 Địa Chỉ Shop Dễ Tìm - Có Chỗ Để Xe Ô Tô Miễn Phí</li>
              </ul>
          </div>
      </div>
  </div>
</footer>
