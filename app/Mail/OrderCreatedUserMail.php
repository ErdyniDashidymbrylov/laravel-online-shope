<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedUserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Order $order,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Заказ #' . $this->order->id . ' успешно оформлен',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.created-user',
        );
    }
}
