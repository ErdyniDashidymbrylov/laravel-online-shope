<?php
namespace App\DTO;

use Spatie\LaravelData\Data;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterDto extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public ?string $phone,
        public string $password,
    ) {}

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
