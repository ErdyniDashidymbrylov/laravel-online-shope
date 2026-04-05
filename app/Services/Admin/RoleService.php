<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\DTO\RoleDto;
use App\Models\Role;
use Exception;

class RoleService
{
    public function create(RoleDto $dto): Role
    {
        return Role::create([
            'name' => $dto->name,
            'slug' => $dto->slug,
        ]);
    }

    public function update(Role $role, RoleDto $dto): Role
    {
        $role->update([
            'name' => $dto->name,
            'slug' => $dto->slug,
        ]);

        return $role->fresh();
    }

    public function delete(Role $role): void
    {
        if (in_array($role->slug, Role::getSystemRoles())) {
            throw new Exception('Нельзя удалить системную роль');
        }

        $role->delete();
    }
}
