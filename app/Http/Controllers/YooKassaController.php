<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Services\YooKassaPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YooKassaController extends Controller
{
    public function __construct(
        private readonly YooKassaPaymentService $yooKassaService
    ) {
    }

    public function return(Order $order): RedirectResponse
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(404);
        }

        $payment = $order->payments()
            ->where('provider', 'yookassa')
            ->latest()
            ->first();

        if ($payment instanceof OrderPayment && $payment->external_payment_id) {
            $this->yooKassaService->synchronizePayment($payment->external_payment_id);
        }

        return redirect()
            ->route('orders.index')
            ->with('success', 'Статус оплаты обновлен.');
    }

    public function webhook(Request $request): JsonResponse
    {
        try {
            $this->yooKassaService->handleWebhook($request->json()->all());

            return response()->json([
                'status' => 'ok',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
