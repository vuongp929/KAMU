<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'status',
        'payment_status',
        'payment_method',
        'total_price',
        'final_total',
        'discount_code',
        'discount_amount',
        'shipping_fee',
        'shipping_address',
    ];

    protected $casts = [
        'shipping_address' => 'array',
    ];

     public function getNameAttribute()
    {
        return $this->shipping_address['name'] ?? 'N/A';
    }

    public function getEmailAttribute()
    {
        return $this->shipping_address['email'] ?? 'N/A';
    }
    
    public function getPhoneAttribute()
    {
        return $this->shipping_address['phone'] ?? 'N/A';
    }

    public function getAddressAttribute()
    {
        return $this->shipping_address['address'] ?? 'N/A';
    }
    // Quan hệ với khách hàng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với các chi tiết đơn hàng
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id'); // user_id là khóa ngoại trong bảng orders
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Lấy thông tin shipping từ JSON
     */
    public function getShippingInfoAttribute()
    {
        if ($this->shipping_address) {
            return json_decode($this->shipping_address, true);
        }
        return null;
    }

    /**
     * Lấy tên khách hàng
     */
    public function getCustomerNameAttribute()
    {
        // Kiểm tra xem shipping_address có phải là JSON không
        $shippingAddress = $this->shipping_address;
        if ($shippingAddress && $this->isJson($shippingAddress)) {
            $shippingInfo = json_decode($shippingAddress, true);
            return $shippingInfo['name'] ?? 'N/A';
        }
        
        // Nếu có user liên kết, lấy tên từ user
        if ($this->user) {
            return $this->user->name;
        }
        
        return 'N/A';
    }

    /**
     * Lấy email khách hàng
     */
    public function getCustomerEmailAttribute()
    {
        // Kiểm tra xem shipping_address có phải là JSON không
        $shippingAddress = $this->shipping_address;
        if ($shippingAddress && $this->isJson($shippingAddress)) {
            $shippingInfo = json_decode($shippingAddress, true);
            return $shippingInfo['email'] ?? 'N/A';
        }
        
        // Nếu có user liên kết, lấy email từ user
        if ($this->user) {
            return $this->user->email;
        }
        
        return 'N/A';
    }

    /**
     * Lấy số điện thoại khách hàng
     */
    public function getCustomerPhoneAttribute()
    {
        // Kiểm tra xem shipping_address có phải là JSON không
        $shippingAddress = $this->shipping_address;
        if ($shippingAddress && $this->isJson($shippingAddress)) {
            $shippingInfo = json_decode($shippingAddress, true);
            return $shippingInfo['phone'] ?? 'N/A';
        }
        
        // Nếu có user liên kết, lấy phone từ user
        if ($this->user && $this->user->phone) {
            return $this->user->phone;
        }
        
        return 'N/A';
    }

    /**
     * Lấy địa chỉ khách hàng
     */
    public function getCustomerAddressAttribute()
    {
        // Kiểm tra xem shipping_address có phải là JSON không
        $shippingAddress = $this->shipping_address;
        if ($shippingAddress && $this->isJson($shippingAddress)) {
            $shippingInfo = json_decode($shippingAddress, true);
            return $shippingInfo['address'] ?? 'N/A';
        }
        
        // Nếu không phải JSON, trả về shipping_address trực tiếp
        return $this->shipping_address ?? 'N/A';
    }

    /**
     * Kiểm tra xem chuỗi có phải là JSON hợp lệ không
     */
    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Cộng điểm thưởng khi thanh toán thành công
     */
    public function addRewardPointsOnPaymentSuccess()
    {
        if ($this->payment_status === 'paid' && $this->user) {
            $this->user->reward_points += 20;
            $this->user->save();
            return true;
        }
        return false;
    }

    /**
     * Kiểm tra xem đơn hàng đã được cộng điểm chưa
     */
    public function hasReceivedRewardPoints()
    {
        // Có thể thêm một cột để track việc đã cộng điểm hay chưa
        // Hoặc dựa vào payment_status
        return $this->payment_status === 'paid';
    }

    /**
     * Kiểm tra xem đơn hàng có đang chờ thanh toán không
     */
    public function isAwaitingPayment()
    {
        return $this->payment_status === 'awaiting_payment';
    }

    /**
     * Kiểm tra xem đơn hàng có phải là thanh toán online không
     */
    public function isOnlinePayment()
    {
        return in_array($this->payment_method, ['vnpay', 'momo']);
    }

    /**
     * Kiểm tra xem đơn hàng có phải là COD không
     */
    public function isCodPayment()
    {
        return $this->payment_method === 'cod';
    }

    /**
     * Chuyển đổi trạng thái thanh toán từ awaiting_payment sang paid
     */
    public function markAsPaid()
    {
        if ($this->payment_status === 'awaiting_payment') {
            // Kiểm tra tồn kho trước khi trừ để tránh overselling
            foreach ($this->items as $orderItem) {
                $variant = $orderItem->variant;
                if (!$variant) {
                    throw new \Exception("Không tìm thấy biến thể sản phẩm cho đơn hàng #{$this->id}");
                }
                
                if ($variant->stock < $orderItem->quantity) {
                    throw new \Exception("Sản phẩm '{$variant->product->name}' đã hết hàng. Còn lại: {$variant->stock}, yêu cầu: {$orderItem->quantity}. Vui lòng chọn mặt hàng khác.");
                }
            }
            
            // Nếu tất cả sản phẩm đều đủ tồn kho, tiến hành trừ
            foreach ($this->items as $orderItem) {
                $variant = $orderItem->variant;
                $variant->decrement('stock', $orderItem->quantity);
            }
            
            $this->payment_status = 'paid';
            $this->status = 'processing';
            $this->save();
            
            // Cộng điểm thưởng cho người dùng khi thanh toán thành công
            $this->addRewardPointsOnPaymentSuccess();
            
            return true;
        }
        return false;
    }

    /**
     * Chuyển đổi trạng thái thanh toán từ awaiting_payment sang failed
     */
    public function markAsPaymentFailed()
    {
        if ($this->payment_status === 'awaiting_payment') {
            $this->payment_status = 'failed';
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Lấy tên hiển thị của trạng thái thanh toán
     */
    public function getPaymentStatusDisplayAttribute()
    {
        $statuses = [
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'cod' => 'Thanh toán khi nhận hàng',
            'awaiting_payment' => 'Đang chờ thanh toán'
        ];

        return $statuses[$this->payment_status] ?? 'Không xác định';
    }

    /**
     * Scope để lấy các đơn hàng chưa thanh toán (bao gồm COD và awaiting_payment)
     */
    public function scopeUnpaidOrders($query)
    {
        return $query->whereIn('payment_status', ['unpaid', 'awaiting_payment', 'cod']);
    }

    /**
     * Scope để lấy các đơn hàng đang chờ thanh toán online
     */
    public function scopeAwaitingPayment($query)
    {
        return $query->where('payment_status', 'awaiting_payment');
    }
}
