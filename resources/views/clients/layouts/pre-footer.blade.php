<style>
/* Footer với chủ đề gấu bông đẹp mắt */
.teddy-footer {
    background: linear-gradient(135deg, #fde2f3 0%, #f8bbd9 25%, #ea73ac 50%, #d63384 75%, #5d3b80 100%);
    padding: 60px 0 30px;
    color: #5d3b80;
    font-family: 'VL BoosterNextFYBlack', sans-serif;
    position: relative;
    overflow: hidden;
    box-shadow: 0 -15px 40px rgba(93, 59, 128, 0.3);
}

/* Hiệu ứng gấu bông nền */
.teddy-footer::before {
    content: '🧸';
    position: absolute;
    top: -50px;
    left: -50px;
    font-size: 200px;
    opacity: 0.05;
    animation: floatTeddy 6s ease-in-out infinite;
    z-index: 0;
}

.teddy-footer::after {
    content: '🧸';
    position: absolute;
    bottom: -50px;
    right: -50px;
    font-size: 150px;
    opacity: 0.08;
    animation: floatTeddy 8s ease-in-out infinite reverse;
    z-index: 0;
}

@keyframes floatTeddy {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

/* Hiệu ứng shimmer */
.teddy-footer .shimmer {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    animation: shimmer 4s infinite;
    z-index: 1;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Container chính */
.teddy-footer .container {
    position: relative;
    z-index: 2;
}

/* Tiêu đề với hiệu ứng gấu bông */
.teddy-footer h5 {
    font-size: 2rem;
    font-weight: bold;
    color: #5d3b80;
    margin-bottom: 25px;
    text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.7);
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
}

.teddy-footer h5::before {
    content: '🧸';
    font-size: 1.5rem;
    animation: teddyBounce 2s ease-in-out infinite;
}

.teddy-footer h5::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #ea73ac, #d63384, #5d3b80);
    border-radius: 2px;
    animation: expandLine 3s ease-in-out infinite;
}

@keyframes teddyBounce {
    0%, 100% { transform: scale(1) rotate(0deg); }
    50% { transform: scale(1.2) rotate(10deg); }
}

@keyframes expandLine {
    0%, 100% { width: 60px; }
    50% { width: 80px; }
}

/* Các khối thông tin */
.footer-section {
    margin-bottom: 40px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 20px;
    backdrop-filter: blur(15px);
    border: 2px solid rgba(234, 115, 172, 0.3);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.footer-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(234, 115, 172, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 0;
}

.footer-section:hover::before {
    opacity: 1;
    animation: ripple 1s ease-out;
}

@keyframes ripple {
    0% { transform: scale(0); }
    100% { transform: scale(1); }
}

.footer-section:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 20px 50px rgba(234, 115, 172, 0.4);
    background: rgba(255, 255, 255, 0.95);
    border-color: #ea73ac;
}

.footer-section > * {
    position: relative;
    z-index: 1;
}

/* Social icons với hiệu ứng gấu bông */
.social-icons {
    display: flex;
    gap: 20px;
    margin-top: 25px;
    flex-wrap: wrap;
}

.social-icons a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #fde2f3, #ea73ac);
    border-radius: 15px;
    padding: 10px;
    transition: all 0.4s ease;
    border: 3px solid transparent;
    position: relative;
    overflow: hidden;
}

.social-icons a::before {
    content: '🧸';
    position: absolute;
    font-size: 12px;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 0;
}

.social-icons a:hover::before {
    opacity: 0.3;
    animation: teddyRotate 0.6s ease;
}

@keyframes teddyRotate {
    0% { transform: rotate(0deg) scale(0); }
    50% { transform: rotate(180deg) scale(1.5); }
    100% { transform: rotate(360deg) scale(1); }
}

.social-icons a:hover {
    transform: scale(1.3) rotate(360deg);
    background: linear-gradient(135deg, #ea73ac, #d63384);
    border-color: #5d3b80;
    box-shadow: 0 10px 25px rgba(234, 115, 172, 0.5);
}

.social-icons img {
    width: 100%;
    height: auto;
    filter: brightness(0) invert(1);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.social-icons a:hover img {
    filter: brightness(1) invert(0);
    transform: scale(1.1);
}

/* Danh sách với icon gấu bông */
ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

ul li {
    margin-bottom: 15px;
    font-size: 1.1rem;
    color: #5d3b80;
    padding: 12px 0;
    border-bottom: 2px solid rgba(93, 59, 128, 0.2);
    transition: all 0.3s ease;
    position: relative;
    padding-left: 35px;
}

ul li::before {
    content: '🧸';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1rem;
    animation: teddyWiggle 3s ease-in-out infinite;
}

@keyframes teddyWiggle {
    0%, 100% { transform: translateY(-50%) rotate(0deg); }
    25% { transform: translateY(-50%) rotate(-5deg); }
    75% { transform: translateY(-50%) rotate(5deg); }
}

ul li:hover {
    padding-left: 45px;
    color: #ea73ac;
    border-bottom-color: #ea73ac;
    background: rgba(234, 115, 172, 0.1);
    border-radius: 8px;
}

ul li:hover::before {
    animation: teddyJump 0.6s ease;
}

@keyframes teddyJump {
    0%, 100% { transform: translateY(-50%) scale(1); }
    50% { transform: translateY(-70%) scale(1.3); }
}

/* Văn bản */
.teddy-footer p {
    margin-bottom: 12px;
    font-size: 1.1rem;
    color: #5d3b80;
    line-height: 1.7;
    text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.5);
}

/* Copyright section */
.copyright-section {
    background: rgba(93, 59, 128, 0.9);
    color: white;
    text-align: center;
    padding: 20px 0;
    margin-top: 30px;
    position: relative;
    overflow: hidden;
}

.copyright-section::before {
    content: '🧸💕🧸💕🧸💕🧸💕🧸💕🧸💕🧸💕🧸💕🧸💕🧸';
    position: absolute;
    top: 50%;
    left: -100%;
    transform: translateY(-50%);
    font-size: 20px;
    opacity: 0.2;
    animation: scrollTeddies 15s linear infinite;
    white-space: nowrap;
}

@keyframes scrollTeddies {
    0% { left: -100%; }
    100% { left: 100%; }
}

.copyright-text {
    position: relative;
    z-index: 1;
    font-size: 1rem;
    font-weight: 500;
}

.copyright-text a {
    color: #ea73ac;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    text-shadow: 0 0 10px rgba(234, 115, 172, 0.5);
}

.copyright-text a:hover {
    color: #fde2f3;
    text-shadow: 0 0 15px rgba(234, 115, 172, 0.8);
    transform: scale(1.05);
}

.footer-section h5 a {
    color: #ea73ac;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    text-shadow: 0 0 10px rgba(234, 115, 172, 0.5);
}

.footer-section h5 a:hover {
    color: #fde2f3;
    text-shadow: 0 0 15px rgba(234, 115, 172, 0.8);
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 768px) {
    .teddy-footer {
        padding: 40px 0 20px;
    }
    
    .footer-section {
        margin-bottom: 25px;
        padding: 20px;
    }
    
    .teddy-footer h5 {
        font-size: 1.6rem;
    }
    
    .social-icons {
        gap: 15px;
        justify-content: center;
    }
    
    .social-icons a {
        width: 45px;
        height: 45px;
    }
    
    ul li {
        font-size: 1rem;
        padding-left: 30px;
    }
}

@media (max-width: 480px) {
    .teddy-footer h5 {
        font-size: 1.4rem;
    }
    
    .footer-section {
        padding: 15px;
    }
    
    .social-icons a {
        width: 40px;
        height: 40px;
    }
}
</style>

<!-- Footer với thiết kế gấu bông đẹp mắt -->
<footer class="teddy-footer">
    <div class="shimmer"></div>
    
    <div class="container">
        <div class="row">
            <!-- Thông tin cửa hàng -->
            <div class="col-md-4">
                <div class="footer-section">
                    <h5><a href="http://127.0.0.1:8000/">KUMAHouse.vn</a> - Ôm Là Yêu</h5>
                    <p>📍 388 Xã Đàn, Đống Đa, Hà Nội</p>
                    <p>📞 096.5555.346 - 096.2222.346</p>
                    <p>💌 Hãy kết nối với gia đình gấu bông của chúng mình!</p>
                    <div class="social-icons">
                        <a href="#" title="Facebook"><img src="https://teddy.vn/wp-content/uploads/2022/08/Icon-Facebook.png" alt="Facebook"></a>
                        <a href="#" title="Địa chỉ"><img src="https://teddy.vn/wp-content/uploads/2022/08/Icon-Location.png" alt="Location"></a>
                        <a href="#" title="Instagram"><img src="https://teddy.vn/wp-content/uploads/2022/08/Icon-Instagram.png" alt="Instagram"></a>
                        <a href="#" title="Youtube"><img src="https://teddy.vn/wp-content/uploads/2022/08/Icon-Youtube.png" alt="Youtube"></a>
                        <a href="#" title="Tiktok"><img src="https://teddy.vn/wp-content/uploads/2024/07/Icon-Tiktok.png" alt="Tiktok"></a>
                    </div>
                </div>
            </div>
            
            <!-- Thông tin thanh toán -->
            <div class="col-md-4">
                <div class="footer-section">
                    <h5>Thông Tin Thanh Toán</h5>
                    <p>🏦 Số tài khoản: <strong>0972926888</strong></p>
                    <p>🏛️ Ngân hàng: <strong>MB Bank</strong></p>
                    <p>👤 Chủ tài khoản: <strong>Nguyễn Thành Trung</strong></p>
                    <p>💳 Hỗ trợ thanh toán online an toàn</p>
                    <p>🔒 Bảo mật thông tin 100%</p>
                </div>
            </div>

            <!-- Dịch vụ Teddy -->
            <div class="col-md-4">
                <div class="footer-section">
                    <h5>6 Lý Do Chọn <a href="http://127.0.0.1:8000/">KUMAHouse.vn</a></h5>
                    <ul>
                        <li>Gói Quà - Nén Nhỏ Gấu - Tặng Thiệp Miễn Phí</li>
                        <li>Giao Hàng Nội Thành Siêu Tốc - Đúng Giờ & Tận Tay</li>
                        <li>Giao Hàng Toàn Quốc 2-5 Ngày - COD Toàn Quốc</li>
                        <li>Bảo Hành Đường Chỉ Vĩnh Viễn - Bảo Hành Bông 1 Năm</li>
                        <li>Dịch Vụ Giặt Gấu & Vệ Sinh Gấu Tại Nhà</li>
                        <li>Địa Chỉ Shop Dễ Tìm - Có Chỗ Để Xe Miễn Phí</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Copyright -->
    <div class="copyright-section">
        <div class="container">
            <div class="copyright-text">
                © 2025 <a href="http://127.0.0.1:8000/">KUMAHouse.vn</a> - Ôm Là Yêu. Tất cả quyền được bảo lưu. 🧸💕
            </div>
        </div>
    </div>
</footer>