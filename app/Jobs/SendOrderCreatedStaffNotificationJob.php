<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\OrderCreatedStaffMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendOrderCreatedStaffNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    /**
     * @param array<int, string> $emails
     */
    public function __construct(
        private readonly int $orderId,
        private readonly array $emails,
    ) {
        $this->onQueue('orders.notifications.staff');
    }

    public function handle(): void
    {
        if ($this->emails === []) {
            return;
        }

        $order = Order::query()
            ->with(['user', 'address', 'items.product'])
            ->find($this->orderId);

        if (!$order) {
            return;
        }

        Mail::to($this->emails)->send(new OrderCreatedStaffMail($order));
    }
}
