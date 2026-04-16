<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\Admin\RoleDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Product;
use App\Models\Role;
use App\Services\Admin\RoleService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {
    }
    public function index(): View
    {
        $roles = Role::query()
            ->withCount('users')
            ->orderBy('id')
            ->get();

        $productsCount = Product::query()->count();

        return view('admin.dashboard', compact('roles', 'productsCount'));
    }

    public function store(StoreRoleRequest $request): JsonResponse|RedirectResponse
    {
        $role = $this->roleService->create(
            RoleDto::fromStoreRequest($request),
        );

        if (!$request->expectsJson()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', "Роль «{$role->name}» создана");
        }

        return response()->json($role, 201);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse|RedirectResponse
    {
        $role = $this->roleService->update(
            $role,
            RoleDto::fromUpdateRequest($request),
        );

        if (!$request->expectsJson()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', "Роль «{$role->name}» обновлена");
        }

        return response()->json($role);
    }

    public function destroy(Role $role): JsonResponse|RedirectResponse
    {
        try {
            $this->roleService->delete($role);
        } catch (Exception $e) {
            if (!request()->expectsJson()) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('error', $e->getMessage());
            }

            return response()->json(['message' => $e->getMessage()], 422);
        }

        if (!request()->expectsJson()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Роль удалена');
        }

        return response()->json(null, 204);
    }
}
