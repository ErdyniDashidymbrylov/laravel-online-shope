<?php

declare(strict_types=1);

namespace App\DTO\Admin;

use App\Http\Requests\Admin\users\UpdateUserRequest;

readonly class UserAdminUpdateDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $role,
    ) {
    }

    public static function fromRequest(UpdateUserRequest $request): self
    {
        return new self(
            name: (string) $request->validated('name'),
            email: (string) $request->validated('email'),
            role: (string) $request->validated('role'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
