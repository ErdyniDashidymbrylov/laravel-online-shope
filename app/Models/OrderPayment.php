<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'status',
        'amount',
        'currency',
        'external_payment_id',
        'idempotence_key',
        'confirmation_url',
        'request_payload',
        'response_payload',
        'paid_at',
        'canceled_at',
        'error_message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'paid_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }
}
