<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Укажите имя таблицы, если оно не совпадает с множественным числом (sales)
    // protected $table = 'sales';

    // Разрешенные для массового заполнения поля
    protected $fillable = [
        'product_id',
        'quantity',
        'price',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];
}
