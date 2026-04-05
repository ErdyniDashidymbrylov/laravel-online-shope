<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id', 'city', 'street', 'house', 'is_default'];

    // Обратная связь (по желанию)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
