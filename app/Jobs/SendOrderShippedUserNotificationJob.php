<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\OrderShippedUserMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderShippedUserNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly int $orderId,
    ) {
        $this->onQueue('orders.notifications.user');
    }

    public function handle(): void
    {
        $order = Order::query()
            ->with(['user', 'address', 'items.product'])
            ->find($this->orderId);

        if (!$order || !$order->user?->email) {
            return;
        }

        Mail::to($order->user->email)->send(new OrderShippedUserMail($order));
    }
}
