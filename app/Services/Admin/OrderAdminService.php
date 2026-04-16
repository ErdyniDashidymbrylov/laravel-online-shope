<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\DTO\Admin\OrderAdminStoreDto;
use App\DTO\Admin\OrderAdminUpdateDto;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderAdminService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->with('user')
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(OrderAdminStoreDto $dto): Order
    {
        return Order::query()->create($dto->toArray());
    }

    public function update(Order $order, OrderAdminUpdateDto $dto): Order
    {
        $order->update($dto->toArray());

        return $order->refresh();
    }

    public function delete(Order $order): bool
    {
        return (bool) $order->delete();
    }

    public function getUsersForSelect(): Collection
    {
        return User::query()
            ->select(['id', 'first_name', 'last_name', 'email'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    public function getStatusesForSelect(): array
    {
        return Order::STATUS_LABELS;
    }
}
