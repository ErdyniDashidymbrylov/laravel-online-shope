<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SendOrderCreatedStaffNotificationJob;
use App\Jobs\SendOrderCreatedUserNotificationJob;
use App\Jobs\SendOrderShippedUserNotificationJob;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;

class OrderNotificationService
{
    public function dispatchOrderCreated(Order $order): void
    {
        $order = $this->loadOrderRelations($order);

        SendOrderCreatedUserNotificationJob::dispatch($order->id);

        $staffEmails = $this->getStaffEmails();

        if ($staffEmails !== []) {
            SendOrderCreatedStaffNotificationJob::dispatch($order->id, $staffEmails);
        }
    }

    public function dispatchOrderShipped(Order $order): void
    {
        $order = $this->loadOrderRelations($order);

        if (!$order->user?->email) {
            return;
        }

        SendOrderShippedUserNotificationJob::dispatch($order->id);
    }

    private function loadOrderRelations(Order $order): Order
    {
        return $order->loadMissing([
            'user',
            'address',
            'items.product',
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function getStaffEmails(): array
    {
        return User::query()
            ->whereNotNull('email')
            ->whereHas('roles', function ($query): void {
                $query->whereIn('slug', [
                    Role::ROLE_ADMIN,
                    Role::ROLE_MANAGER,
                ]);
            })
            ->distinct()
            ->pluck('email')
            ->filter(static fn (?string $email): bool => filled($email))
            ->values()
            ->all();
    }
}
