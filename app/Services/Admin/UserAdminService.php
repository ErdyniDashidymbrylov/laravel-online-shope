<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\DTO\Admin\UserAdminResetPasswordDto;
use App\DTO\Admin\UserAdminStoreDto;
use App\DTO\Admin\UserAdminUpdateDto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserAdminService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->latest('id')
            ->paginate($perPage);
    }

    public function create(UserAdminStoreDto $dto): User
    {
        $data = $dto->toArray();
        $data['password'] = Hash::make($dto->password);

        $user = User::query()->create($data);
        $this->syncRole($user, $dto->role);

        return $user->refresh();
    }

    public function update(User $user, UserAdminUpdateDto $dto): User
    {
        $user->update($dto->toArray());
        $this->syncRole($user, $dto->role);

        return $user->refresh()->load('roles');
    }

    public function resetPassword(User $user, UserAdminResetPasswordDto $dto): User
    {
        $user->update([
            'password' => Hash::make($dto->password),
        ]);

        return $user->refresh();
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    public function getRolesForSelect(): Collection
    {
        return Role::query()
            ->select(['id', 'name', 'slug'])
            ->orderBy('name')
            ->get();
    }

    private function syncRole(User $user, string $roleSlug): void
    {
        $role = Role::query()
            ->where('slug', $roleSlug)
            ->firstOrFail();

        $user->roles()->sync([$role->id]);
    }
}
