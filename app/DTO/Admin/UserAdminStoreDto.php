<?php

declare(strict_types=1);

namespace App\DTO\Admin;

use App\Http\Requests\Admin\users\StoreUserRequest;

readonly class UserAdminStoreDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role,
    ) {
    }

    public static function fromRequest(StoreUserRequest $request): self
    {
        return new self(
            name: (string) $request->validated('name'),
            email: (string) $request->validated('email'),
            password: (string) $request->validated('password'),
            role: (string) $request->validated('role'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
