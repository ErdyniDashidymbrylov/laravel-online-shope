<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\SessionCartService;
use App\Services\YooKassaPaymentService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(): Factory|View
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with(['items.product'])
            ->orderByDesc('created_at')
            ->get();

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function store(
        OrderStoreRequest $request,
        OrderService $service,
        YooKassaPaymentService $yooKassaPaymentService,
        SessionCartService $cart
    ): RedirectResponse {
        $user = Auth::user();

        try {
            $order = $service->createOrder(
                $user,
                $request->validated()['payment_method'],
                [
                    'city' => (string) $request->validated('city'),
                    'street' => (string) $request->validated('street'),
                    'house' => (string) $request->validated('house'),
                    'save_address' => (bool) $request->validated('save_address'),
                ],
                $cart
            );

            if ($order->payment_method === Order::PAYMENT_METHOD_YOOKASSA) {
                $payment = $yooKassaPaymentService->createPaymentForOrder($order);

                if ($payment->confirmation_url) {
                    return redirect()->away($payment->confirmation_url);
                }

                return redirect()
                    ->route('orders.index')
                    ->withErrors([
                        'payment' => 'YooKassa не вернула ссылку для оплаты.',
                    ]);
            }

            return redirect()
                ->route('orders.index')
                ->with('success', 'Заказ создан.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    public function updateStatus(
        Order $order,
        OrderStatusRequest $request,
        OrderService $service
    ): RedirectResponse {
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->whereKey($order->id)
            ->firstOrFail();

        $status = $request->validated()['status'];

        if ($status === Order::STATUS_PAID) {
            $service->markAsPaid($order);
            $message = 'Заказ оплачен.';
        } else {
            $service->cancel($order);
            $message = 'Заказ отменен.';
        }

        return redirect()
            ->route('orders.index')
            ->with('success', $message);
    }



}
