<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel; // <-- Quan trọng: Dùng PrivateChannel
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User; // Import User model
use App\Models\Message; // Import Message model

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Giờ đây event sẽ mang theo toàn bộ đối tượng Message
    public Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Phát sóng trên một kênh riêng tư, duy nhất cho cuộc trò chuyện này
        // Tên kênh sẽ có dạng: private-chat.1.5 (giữa user 1 và user 5)
        $participants = [$this->message->sender_id, $this->message->receiver_id];
        sort($participants); // Sắp xếp ID để đảm bảo tên kênh luôn nhất quán
        $channelName = 'chat.' . implode('.', $participants);

        return [
            new PrivateChannel($channelName),
        ];
    }
    
    /**
     * Tên của sự kiện khi được phát đi.
     */
    public function broadcastAs(): string
    {
        return 'new-message';
    }
}