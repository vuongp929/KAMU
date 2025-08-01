<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;

class ChatController extends Controller
{
    public function index()
    {
        // Lấy ID của tất cả các user đã gửi tin nhắn hoặc nhận tin nhắn từ admin (ID = 1)
        $userIds = Message::where('sender_id', 1)
                          ->orWhere('receiver_id', 1)
                          ->distinct()
                          ->pluck('sender_id')
                          ->merge(Message::where('sender_id', 1)
                                       ->orWhere('receiver_id', 1)
                                       ->distinct()
                                       ->pluck('receiver_id'))
                          ->unique();

        // Lấy thông tin của các user đó, loại trừ chính admin
        $users = User::whereIn('id', $userIds)
                     ->where('id', '!=', 1) // Loại bỏ admin ra khỏi danh sách
                     ->get();

        return view('admins.chat.index', compact('users'));
    }
    public function searchUsers(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ query string (?q=...)
        $query = $request->input('q', '');

        // Nếu không có từ khóa, trả về mảng rỗng
        if (empty($query)) {
            return response()->json([]);
        }

        // Tìm kiếm user có tên hoặc email chứa từ khóa
        // Loại trừ admin và chỉ lấy tối đa 10 kết quả
        $users = User::where('id', '!=', 1) // Luôn loại trừ admin
                     ->where(function($q) use ($query) {
                         $q->where('name', 'LIKE', "%{$query}%")
                           ->orWhere('email', 'LIKE', "%{$query}%");
                     })
                     ->limit(10)
                     ->get(['id', 'name', 'email']); // Chỉ lấy các cột cần thiết

        return response()->json($users);
    }
}