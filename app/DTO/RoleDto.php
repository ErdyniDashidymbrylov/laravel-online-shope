<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use Illuminate\Support\Str;

class RoleDto
{
    public function __construct(
        public string $name,
        public string $slug,
    ) {
    }

    public static function fromStoreRequest(StoreRoleRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            slug: Str::slug($request->validated('name')),
        );
    }

    public static function fromUpdateRequest(UpdateRoleRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            slug: Str::slug($request->validated('name')),
        );
    }
}
