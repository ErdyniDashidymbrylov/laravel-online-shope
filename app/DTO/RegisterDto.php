<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\Auth\RegisterRequest;
use Spatie\LaravelData\Data;

class RegisterDto extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public ?string $phone,
        public string $password,
    ) {
    }

    public static function fromRequest(RegisterRequest $request): self
    {
        $data = $request->validated();

        return new self(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['password'],
        );
    }
}
