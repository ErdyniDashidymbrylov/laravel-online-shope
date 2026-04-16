<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\Admin\UserAdminResetPasswordDto;
use App\DTO\Admin\UserAdminStoreDto;
use App\DTO\Admin\UserAdminUpdateDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\users\ResetUserPasswordRequest;
use App\Http\Requests\Admin\users\StoreUserRequest;
use App\Http\Requests\Admin\users\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    public function __construct(
        private readonly UserAdminService $userAdminService
    ) {
    }

    public function index(): View
    {
        $users = $this->userAdminService->paginate();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = $this->userAdminService->getRolesForSelect();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->userAdminService->create(UserAdminStoreDto::fromRequest($request));

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Пользователь создан.');
    }

    public function show(User $user): View
    {
        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $user->load('roles');
        $roles = $this->userAdminService->getRolesForSelect();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $dto = UserAdminUpdateDto::fromRequest($request);
        $this->userAdminService->update($user, $dto);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Пользователь обновлён.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userAdminService->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Пользователь удалён.');
    }

    public function resetPassword(
        ResetUserPasswordRequest $request,
        User $user
    ): RedirectResponse {
        $dto = UserAdminResetPasswordDto::fromRequest($request);
        $this->userAdminService->resetPassword($user, $dto);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'Пароль пользователя обновлён.');
    }
}
