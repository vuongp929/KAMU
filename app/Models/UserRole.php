<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    use HasFactory;

    /**
     * Tên của bảng trong database mà model này đại diện.
     * Laravel thường có thể tự đoán (user_roles), nhưng việc khai báo rõ ràng
     * sẽ giúp code chắc chắn và dễ hiểu hơn.
     *
     * @var string
     */
    protected $table = 'user_roles';

    /**
     * Các thuộc tính có thể được gán hàng loạt (mass assignable).
     * Điều này là cần thiết để bảo mật và cho phép sử dụng các phương thức
     * như UserRole::create([...]).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'role',
    ];

    /**
     * Định nghĩa mối quan hệ ngược: một vai trò (UserRole) thuộc về một người dùng (User).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}