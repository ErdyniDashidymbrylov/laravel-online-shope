<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\Admin\OrderAdminStoreDto;
use App\DTO\Admin\OrderAdminUpdateDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\orders\StoreOrderRequest;
use App\Http\Requests\Admin\orders\UpdateOrderRequest;
use App\Models\Order;
use App\Services\Admin\OrderAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderAdminController extends Controller
{
    public function __construct(
        private readonly OrderAdminService $orderAdminService
    ) {
    }

    public function index(): View
    {
        $orders = $this->orderAdminService->paginate();

        return view('admin.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $users = $this->orderAdminService->getUsersForSelect();
        $statuses = $this->orderAdminService->getStatusesForSelect();

        return view('admin.orders.create', compact('users', 'statuses'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $dto = OrderAdminStoreDto::fromRequest($request);
        $order = $this->orderAdminService->create($dto);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Заказ создан.');
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'address', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $users = $this->orderAdminService->getUsersForSelect();
        $statuses = $this->orderAdminService->getStatusesForSelect();

        return view('admin.orders.edit', compact('order', 'users', 'statuses'));
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $dto = OrderAdminUpdateDto::fromRequest($request);
        $this->orderAdminService->update($order, $dto);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Заказ обновлён.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->orderAdminService->delete($order);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Заказ удалён.');
    }
}
