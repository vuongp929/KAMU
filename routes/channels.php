<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Kênh xác thực cho các cuộc trò chuyện riêng tư.
 * Tên kênh có dạng: chat.{userId1}.{userId2}
 */
Broadcast::channel('chat.{userId1}.{userId2}', function (User $user, int $userId1, int $userId2) {
    // Chỉ cho phép người dùng lắng nghe kênh này nếu ID của họ là 1 trong 2 người tham gia
    return $user->id === $userId1 || $user->id === $userId2;
});