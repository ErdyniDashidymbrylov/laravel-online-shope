<?php

declare(strict_types=1);

namespace App\DTO\Admin;

use App\Http\Requests\Admin\users\ResetUserPasswordRequest;

readonly class UserAdminResetPasswordDto
{
    public function __construct(
        public string $password,
    ) {
    }

    public static function fromRequest(ResetUserPasswordRequest $request): self
    {
        return new self(
            password: (string) $request->validated('password'),
        );
    }
}
