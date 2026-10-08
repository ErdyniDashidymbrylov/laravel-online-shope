<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentReceipt;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class YooKassaPaymentService
{
    private const PROVIDER = 'yookassa';

    public function __construct(
        private readonly HttpFactory $http,
        private readonly OrderService $orderService,
    ) {
    }

    public function createPaymentForOrder(Order $order): OrderPayment
    {
        if ($order->payment_method !== Order::PAYMENT_METHOD_YOOKASSA) {
            throw ValidationException::withMessages([
                'payment_method' => 'Для заказа выбран другой способ оплаты.',
            ]);
        }

        $existingPayment = $order->payments()
            ->where('provider', self::PROVIDER)
            ->whereIn('status', ['pending', 'waiting_for_capture'])
            ->latest()
            ->first();

        if ($existingPayment instanceof OrderPayment && $existingPayment->confirmation_url) {
            return $existingPayment;
        }

        $idempotenceKey = (string) Str::uuid();
        $payload = $this->paymentPayload($order);
        $response = $this->request($idempotenceKey)
            ->post($this->url('/payments'), $payload);

        $this->throwUnlessSuccessful($response);

        $data = $response->json();

        return $order->payments()->create([
            'provider' => self::PROVIDER,
            'status' => (string) data_get($data, 'status', 'pending'),
            'amount' => $order->total,
            'currency' => $this->currency(),
            'external_payment_id' => (string) data_get($data, 'id'),
            'idempotence_key' => $idempotenceKey,
            'confirmation_url' => data_get($data, 'confirmation.confirmation_url'),
            'request_payload' => $payload,
            'response_payload' => $data,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function handleWebhook(array $payload): array
    {
        $paymentId = (string) data_get($payload, 'object.id');

        if ($paymentId === '') {
            throw ValidationException::withMessages([
                'payment' => 'Webhook YooKassa не содержит идентификатор платежа.',
            ]);
        }

        return $this->synchronizePayment($paymentId);
    }

    /**
     * @return array<string, mixed>
     */
    public function synchronizePayment(string $paymentId): array
    {
        $response = $this->request()
            ->get($this->url('/payments/' . $paymentId));

        $this->throwUnlessSuccessful($response);

        $paymentData = $response->json();
        $orderPayment = $this->findOrderPayment($paymentData);
        $status = (string) data_get($paymentData, 'status');

        DB::transaction(function () use ($orderPayment, $paymentData, $status): void {
            $orderPayment->update([
                'status' => $status,
                'response_payload' => $paymentData,
                'paid_at' => $status === 'succeeded' ? now() : $orderPayment->paid_at,
                'canceled_at' => $status === 'canceled' ? now() : $orderPayment->canceled_at,
            ]);

            $order = $orderPayment->order()->lockForUpdate()->firstOrFail();

            if ($status === 'succeeded' && $order->status === Order::STATUS_PENDING) {
                $this->orderService->markAsPaid($order);
                $this->createReceipt($orderPayment);
            }

            if ($status === 'canceled' && $order->status === Order::STATUS_PENDING) {
                $order->update([
                    'status' => Order::STATUS_CANCELED,
                ]);
            }
        });

        return $paymentData;
    }

    public function createReceipt(OrderPayment $payment): ?PaymentReceipt
    {
        if (
            !config('services.yookassa.receipts.enabled')
            || config('services.yookassa.receipts.mode') === 'embedded'
        ) {
            return null;
        }

        if ($payment->receipts()->exists()) {
            return $payment->receipts()->latest()->first();
        }

        $payment->loadMissing('order.user', 'order.items.product');
        $idempotenceKey = (string) Str::uuid();
        $payload = $this->receiptPayload($payment);
        $receipt = $payment->receipts()->create([
            'type' => 'payment',
            'status' => 'pending',
            'send_to_customer' => true,
            'request_payload' => $payload,
        ]);

        $response = $this->request($idempotenceKey)
            ->post($this->url('/receipts'), $payload);

        if (!$response->successful()) {
            $receipt->update([
                'status' => 'failed',
                'response_payload' => $response->json(),
                'error_message' => $response->body(),
            ]);

            return $receipt;
        }

        $data = $response->json();

        $receipt->update([
            'external_receipt_id' => data_get($data, 'id'),
            'status' => (string) data_get($data, 'status', 'pending'),
            'response_payload' => $data,
        ]);

        return $receipt;
    }

    private function findOrderPayment(array $paymentData): OrderPayment
    {
        $paymentId = (string) data_get($paymentData, 'id');
        $orderPayment = OrderPayment::query()
            ->where('provider', self::PROVIDER)
            ->where('external_payment_id', $paymentId)
            ->first();

        if ($orderPayment instanceof OrderPayment) {
            return $orderPayment;
        }

        $orderId = data_get($paymentData, 'metadata.order_id');

        if ($orderId) {
            $order = Order::query()->find($orderId);

            if ($order instanceof Order) {
                return $order->payments()->create([
                    'provider' => self::PROVIDER,
                    'status' => (string) data_get($paymentData, 'status', 'pending'),
                    'amount' => (string) data_get($paymentData, 'amount.value', $order->total),
                    'currency' => (string) data_get($paymentData, 'amount.currency', $this->currency()),
                    'external_payment_id' => $paymentId,
                    'response_payload' => $paymentData,
                ]);
            }
        }

        throw ValidationException::withMessages([
            'payment' => 'Платеж YooKassa не связан с заказом.',
        ]);
    }

    private function request(?string $idempotenceKey = null): \Illuminate\Http\Client\PendingRequest
    {
        $request = $this->http
            ->timeout((int) config('services.yookassa.timeout', 15))
            ->acceptJson()
            ->asJson()
            ->withBasicAuth($this->shopId(), $this->secretKey());

        if ($idempotenceKey !== null) {
            $request->withHeaders([
                'Idempotence-Key' => $idempotenceKey,
            ]);
        }

        return $request;
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentPayload(Order $order): array
    {
        $payload = [
            'amount' => [
                'value' => $this->money($order->total),
                'currency' => $this->currency(),
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('yookassa.return', ['order' => $order->id]),
            ],
            'description' => Str::limit('Оплата заказа №' . $order->id, 128, ''),
            'metadata' => [
                'order_id' => (string) $order->id,
                'user_id' => (string) $order->user_id,
            ],
        ];

        if (config('services.yookassa.receipts.enabled') && config('services.yookassa.receipts.mode') === 'embedded') {
            $payload['receipt'] = $this->receiptData($order);
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function receiptPayload(OrderPayment $payment): array
    {
        return [
            'type' => 'payment',
            'payment_id' => $payment->external_payment_id,
            'customer' => $this->receiptCustomer($payment->order),
            'send' => true,
            'items' => $this->receiptItems($payment->order),
            'settlements' => [
                [
                    'type' => (string) config('services.yookassa.receipts.settlement_type', 'cashless'),
                    'amount' => [
                        'value' => $this->money($payment->amount),
                        'currency' => $payment->currency,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function receiptData(Order $order): array
    {
        $receipt = [
            'customer' => $this->receiptCustomer($order),
            'items' => $this->receiptItems($order),
        ];

        $taxSystemCode = config('services.yookassa.receipts.tax_system_code');

        if ($taxSystemCode !== null && $taxSystemCode !== '') {
            $receipt['tax_system_code'] = (int) $taxSystemCode;
        }

        return $receipt;
    }

    /**
     * @return array<string, string>
     */
    private function receiptCustomer(Order $order): array
    {
        $email = $order->user?->email;

        if (!$email) {
            throw ValidationException::withMessages([
                'email' => 'Для онлайн-оплаты нужен email покупателя.',
            ]);
        }

        return ['email' => $email];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function receiptItems(Order $order): array
    {
        $order->loadMissing('items.product');

        return $order->items->map(function ($item): array {
            return [
                'description' => Str::limit($item->product?->name ?? 'Товар', 128, ''),
                'quantity' => (string) $item->quantity,
                'amount' => [
                    'value' => $this->money($item->price),
                    'currency' => $this->currency(),
                ],
                'vat_code' => (int) config('services.yookassa.receipts.vat_code', 1),
                'payment_mode' => (string) config('services.yookassa.receipts.payment_mode', 'full_payment'),
                'payment_subject' => (string) config('services.yookassa.receipts.payment_subject', 'commodity'),
            ];
        })->values()->all();
    }

    private function throwUnlessSuccessful(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        throw ValidationException::withMessages([
            'payment' => 'Ошибка YooKassa: ' . $response->body(),
        ]);
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.yookassa.base_url'), '/') . $path;
    }

    private function shopId(): string
    {
        return (string) config('services.yookassa.shop_id');
    }

    private function secretKey(): string
    {
        return (string) config('services.yookassa.secret_key');
    }

    private function currency(): string
    {
        return (string) config('services.yookassa.currency', 'RUB');
    }

    private function money(string|float|int $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }
}
