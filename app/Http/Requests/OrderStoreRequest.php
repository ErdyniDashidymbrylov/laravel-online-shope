<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::in(Order::PAYMENT_METHODS)],
            'city' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'house' => ['required', 'string', 'max:50'],
            'save_address' => ['nullable', 'boolean'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'save_address' => $this->boolean('save_address'),
        ]);
    }
}
