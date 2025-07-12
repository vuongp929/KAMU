<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Lấy các đánh giá sản phẩm của người dùng.
     */
    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    // ==========================================================
    // === BẮT ĐẦU PHẦN THÊM MỚI =================================
    // ==========================================================

    /**
     * Lấy các vai trò (roles) của người dùng từ bảng user_role.
     * Một User có thể có nhiều vai trò.
     */
    public function roles(): HasMany
    {
        // Laravel sẽ tự động tìm foreign key 'user_id' trong bảng 'user_roles'
        // Dựa trên tên của model là 'User'.
        return $this->hasMany(UserRole::class);
    }

    /**
     * Hàm kiểm tra nhanh xem người dùng có phải là Admin hay không.
     * Hàm này rất hữu ích, đặc biệt trong các file Blade hoặc Middleware.
     * @return bool
     */
    public function isAdmin(): bool
    {
        // 'roles' là tên của mối quan hệ (relationship) chúng ta vừa định nghĩa ở trên.
        // where('role', 'admin') lọc ra những vai trò có tên là 'admin'.
        // exists() sẽ trả về true nếu có ít nhất 1 kết quả, ngược lại trả về false.
        return $this->roles()->where('role', 'admin')->exists();
    }

    // ==========================================================
    // === KẾT THÚC PHẦN THÊM MỚI ================================
    // ==========================================================

    public function role()
    {
        return $this->hasOne(UserRole::class);
    }
}