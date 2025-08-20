<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Role::query();

        // Search by name or display_name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $roles = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'data' => RoleResource::collection($roles),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
            ]
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(RoleStoreRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());

        return response()->json([
            'data' => new RoleResource($role),
            'message' => 'Role created successfully'
        ], 201);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'data' => new RoleResource($role->load('permissions'))
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(RoleUpdateRequest $request, Role $role): JsonResponse
    {
        $role->update($request->validated());

        return response()->json([
            'data' => new RoleResource($role),
            'message' => 'Role updated successfully'
        ]);
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): JsonResponse
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role. It has assigned users.'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Get active roles only.
     */
    public function getActive(): JsonResponse
    {
        $roles = Role::active()->get();

        return response()->json([
            'data' => RoleResource::collection($roles)
        ]);
    }

    /**
     * Get inactive roles only.
     */
    public function getInactive(): JsonResponse
    {
        $roles = Role::inactive()->get();

        return response()->json([
            'data' => RoleResource::collection($roles)
        ]);
    }

    /**
     * Activate a role.
     */
    public function activate(Role $role): JsonResponse
    {
        $role->activate();

        return response()->json([
            'data' => new RoleResource($role),
            'message' => 'Role activated successfully'
        ]);
    }

    /**
     * Deactivate a role.
     */
    public function deactivate(Role $role): JsonResponse
    {
        $role->deactivate();

        return response()->json([
            'data' => new RoleResource($role),
            'message' => 'Role deactivated successfully'
        ]);
    }

    /**
     * Get assigned permissions for a role
     */
    public function permissions(Role $role): JsonResponse
    {
        $permissions = $role->permissions()->get();

        return response()->json([
            'data' => $permissions,
        ]);
    }

    /**
     * Attach permissions to role
     */
    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permission_ids' => 'array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permission_ids'] ?? []);

        return response()->json([
            'data' => [
                'role' => new RoleResource($role->load('permissions')),
            ],
            'message' => 'Permissions updated successfully',
        ]);
    }
}
