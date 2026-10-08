<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class DailySalesReport extends Model
{
    protected $fillable = [
        'report_date',
        'orders_count',
        'sales_count',
        'revenue',
        'canceled_count',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'revenue' => 'decimal:2',
            'calculated_at' => 'datetime',
        ];
    }

    protected function reportDate(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): Carbon => Carbon::parse($value),
            set: fn (Carbon|string $value): string => Carbon::parse($value)->toDateString(),
        );
    }
}
