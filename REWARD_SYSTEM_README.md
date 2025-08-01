# Hệ Thống Điểm Thưởng - KAMU

## Tổng Quan
Hệ thống điểm thưởng cho phép khách hàng tích điểm từ các đơn hàng thành công và quy đổi thành mã giảm giá.

## Tính Năng Chính

### 1. Tích Điểm Tự Động
- **+20 điểm** cho mỗi đơn hàng đã thanh toán thành công
- Điểm được cộng tự động khi thanh toán thành công
- Bắt buộc thanh toán thành công mới được nhận điểm
- Không giới hạn số lượng đơn hàng

### 2. Quy Đổi Điểm Thưởng
- **Hệ thống mức cố định**:
  - 100 điểm = 5% giảm giá
  - 200 điểm = 6% giảm giá
  - 400 điểm = 7% giảm giá
  - 600 điểm = 8% giảm giá
  - 800 điểm = 9% giảm giá
  - 1000 điểm = 10% giảm giá (tối đa)
- Mã giảm giá có hiệu lực trong **3 tháng**
- Áp dụng cho đơn hàng từ **100,000 VND**

### 3. Quản Lý Điểm Thưởng
- Xem điểm thưởng hiện tại
- Lịch sử đơn hàng đã hoàn thành
- Thống kê tổng điểm đã tích

## Cấu Trúc Database

### Bảng `users` (Đã cập nhật)
```sql
ALTER TABLE users ADD COLUMN reward_points INT DEFAULT 0 AFTER password;
```

### Bảng `orders` (Sử dụng cột hiện có)
- Sử dụng cột `shipping_address` để lưu thông tin khách hàng dưới dạng JSON
- Thông tin bao gồm: name, email, phone, address

### Bảng `discounts` (Đã có sẵn)
- Sử dụng bảng discounts hiện có để lưu mã giảm giá từ điểm thưởng

### Bảng `user_discount_codes` (Mới tạo)
- Lưu thông tin mã giảm giá của từng người dùng
- Theo dõi trạng thái sử dụng và thời hạn

## Các File Đã Tạo/Cập Nhật

### 1. Migration
- `database/migrations/2025_01_15_000000_add_reward_points_to_users_table.php`
- `database/migrations/2025_01_15_000001_create_user_discount_codes_table.php`

### 2. Models
- `app/Models/User.php` - Thêm các method quản lý điểm thưởng và quan hệ với mã giảm giá
- `app/Models/Order.php` - Thêm accessors để lấy thông tin khách hàng từ JSON
- `app/Models/UserDiscountCode.php` - Model quản lý mã giảm giá của người dùng

### 3. Controllers
- `app/Http/Controllers/Client/RewardController.php` - Controller quản lý điểm thưởng
- `app/Http/Controllers/Client/CheckoutController.php` - Đặt hàng (không cộng điểm ngay)
- `app/Http/Controllers/Client/PaymentController.php` - Xử lý thanh toán và cộng điểm thưởng
- `app/Http/Controllers/Client/MyOrderController.php` - Quản lý đơn hàng

### 4. Views
- `resources/views/clients/points/index.blade.php` - Trang quản lý điểm thưởng
- `resources/views/clients/points/history.blade.php` - Lịch sử điểm thưởng
- `resources/views/clients/points/discount-codes.blade.php` - Trang mã đổi thưởng (mới tạo)
- `resources/views/clients/orders/show.blade.php` - Chi tiết đơn hàng (mới tạo)
- `resources/views/clients/account/layout.blade.php` - Thêm menu điểm thưởng và mã đổi thưởng
- `resources/views/clients/orders/index.blade.php` - Hiển thị điểm thưởng trong đơn hàng

### 5. Routes
- `routes/web.php` - Thêm routes cho điểm thưởng

## Cách Sử Dụng

### 1. Chạy Migration
```bash
php artisan migrate
```

### 2. Truy Cập Chức Năng
- **Đăng nhập** vào tài khoản khách hàng
- Vào **Tài Khoản Của Tôi** → **Điểm Thưởng**
- Hoặc vào **Đơn Mua** → **Điểm Thưởng**

### 3. Quy Trình Tích Điểm
1. Khách hàng đặt hàng → Chưa nhận điểm
2. Khách hàng thanh toán thành công → Nhận +20 điểm
3. Với thanh toán COD: Khách hàng xác nhận đã thanh toán → Nhận +20 điểm
4. Điểm được cộng vào tài khoản ngay khi thanh toán thành công

### 4. Quy Đổi Điểm
1. Vào trang **Điểm Thưởng**
2. Chọn số điểm muốn quy đổi từ dropdown (100-1,000 điểm)
3. Nhấn **Quy Đổi Ngay**
4. Nhận mã giảm giá tự động
5. **Tỷ lệ quy đổi**:
   - 100 điểm = 5% giảm giá
   - 200 điểm = 6% giảm giá
   - 400 điểm = 7% giảm giá
   - 600 điểm = 8% giảm giá
   - 800 điểm = 9% giảm giá
   - 1000 điểm = 10% giảm giá

### 5. Sử Dụng Mã Giảm Giá
1. Vào trang **Thanh Toán** khi đặt hàng
2. Nhập mã giảm giá vào ô **"Nhập mã giảm giá"**
3. Nhấn **"Áp dụng"** để kiểm tra và áp dụng mã
4. Hệ thống sẽ tự động tính toán giảm giá
5. **Lưu ý**: Mã đổi thưởng chỉ áp dụng cho đơn hàng từ 100,000 VND

## API Endpoints

### Routes cho Khách Hàng
```php
// Xem điểm thưởng
GET /rewards

// Quy đổi điểm
POST /rewards/exchange

// Lịch sử điểm thưởng
GET /rewards/history

// Mã đổi thưởng
GET /rewards/discount-codes

// Hoàn thành đơn hàng
POST /my-orders/{order}/complete

// Chi tiết đơn hàng
GET /my-orders/{order}

// Xác nhận thanh toán COD
POST /payment/cod/{order}

// Validate mã giảm giá (AJAX)
POST /checkout/validate-discount
POST /payment/cod/{order}

// Thanh toán thành công
POST /payment/success

// Thanh toán thất bại
POST /payment/failed
```

## Sửa Lỗi Database

### Vấn đề đã gặp:
- Lỗi khi tạo đơn hàng: cột `email`, `name`, `phone`, `address` không tồn tại trong bảng `orders`

### Giải pháp đã áp dụng:
1. **Sử dụng cột `shipping_address` hiện có** để lưu thông tin khách hàng dưới dạng JSON
2. **Thêm accessors trong Order model** để dễ dàng truy cập thông tin:
   - `customer_name`
   - `customer_email`
   - `customer_phone`
   - `customer_address`
3. **Cập nhật CheckoutController** để lưu thông tin vào JSON thay vì các cột riêng biệt

### Cấu trúc JSON trong shipping_address:
```json
{
    "name": "Tên khách hàng",
    "email": "email@example.com",
    "phone": "0123456789",
    "address": "Địa chỉ giao hàng"
}
```

## Bảo Mật
- Chỉ khách hàng đã đăng nhập mới có thể truy cập
- Kiểm tra quyền sở hữu đơn hàng trước khi thao tác
- Validate dữ liệu đầu vào khi quy đổi điểm

## Tùy Chỉnh
- Có thể thay đổi số điểm cộng cho mỗi đơn hàng trong `CheckoutController`
- Có thể điều chỉnh tỷ lệ quy đổi trong `RewardController`
- Có thể thay đổi thời hạn mã giảm giá

## Lưu Ý
- Điểm thưởng không có thời hạn
- Mã giảm giá chỉ sử dụng được 1 lần
- Hệ thống tự động kiểm tra đủ điểm trước khi quy đổi
- Giao dịch quy đổi được thực hiện trong transaction để đảm bảo tính nhất quán
- Thông tin khách hàng được lưu trong JSON để tương thích với cấu trúc database hiện tại 