<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address; // <-- Thêm dòng này


class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $confirmationUrl;


    /**
     * Create a new message instance.
     */
     public function __construct(Order $order, string $confirmationUrl)
    {
        $this->order = $order;
        $this->confirmationUrl = $confirmationUrl; // Gán giá trị
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đơn hàng #' . $this->order->id . ' tại Ôm Là Yêu',
        );
    }


    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Trỏ đến file view mà chúng ta sẽ tạo ở bước tiếp theo
        return new Content(
            view: 'emails.orders.confirmation',
            with: [
                'confirmationUrl' => $this->confirmationUrl,
            ],
        );
    }
}