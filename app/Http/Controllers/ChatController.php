<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\NewChatMessage;
use App\Models\Message; // Import Message model
use App\Models\User;   // Import User model

class ChatController extends Controller
{
    /**
     * Nhận và lưu tin nhắn, sau đó phát event lên kênh riêng tư.
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'receiver_id' => 'required|integer|exists:users,id' // Bắt buộc phải có người nhận
        ]);

        $sender = Auth::user();

        // 1. Lưu tin nhắn vào database
        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
        ]);
        
        // Tải trước thông tin người gửi để gửi kèm trong event
        $message->load('sender');

        // 2. Phát event NewChatMessage lên Pusher
        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json(['status' => 'Message sent successfully', 'message' => $message]);
    }

    /**
     * Lấy lịch sử chat giữa người dùng hiện tại và một người dùng khác.
     */
    public function getHistory($receiverId)
    {
        $senderId = Auth::id();

        // Tìm user có ID là $receiverId, nếu không thấy sẽ báo lỗi 404
        User::findOrFail($receiverId);

        $messages = Message::where(function($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })->orWhere(function($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })
        ->with('sender') // Tải trước thông tin người gửi
        ->orderBy('created_at', 'asc')
        ->get();
        
        return response()->json($messages);
    }
}