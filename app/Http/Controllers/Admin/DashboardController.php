<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTO\RoleDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use App\Services\Admin\RoleService;
use Exception;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {
    }
    public function index()
    {
        return view('admin.dashboard');
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create(
            RoleDto::fromStoreRequest($request),
        );

        return response()->json($role, 201);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->roleService->update(
            $role,
            RoleDto::fromUpdateRequest($request),
        );

        return response()->json($role);
    }

    public function destroy(Role $role): JsonResponse
    {
        try {
            $this->roleService->delete($role);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(null, 204);
    }
}
