<?php

namespace App\DTO;

use App\Http\Requests\UpdateProfileRequest;

class UpdateProfileDto
{
    public function __construct(
        public ?string $first_name,
        public ?string $last_name,
        public ?string $email,
        public ?string $phone,
    ) {}

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        $data = $request->validated();

        return new self(
            $data['first_name'] ?? null,
            $data['last_name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
        ], static fn ($value) => !is_null($value));
    }
}
