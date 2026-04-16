<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\orders;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'string', Rule::in(array_keys(Order::STATUS_LABELS))],
            'total' => ['required', 'numeric', 'min:0'],
        ];
    }
}
