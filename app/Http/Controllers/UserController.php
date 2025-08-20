<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('role');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by role
        if ($request->has('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Hash the password
        $data['password'] = Hash::make($data['password']);
        // Default role to 1 if not provided
        if (!isset($data['role_id']) || $data['role_id'] === null) {
            $data['role_id'] = 1;
        }
        
        $user = User::create($data);

        return response()->json([
            'data' => new UserResource($user->load('role')),
            'message' => 'User created successfully'
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($user->load('role'))
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        
        // Hash the password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);

        return response()->json([
            'data' => new UserResource($user->load('role')),
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deletion of the last admin user
        if ($user->isAdmin() && User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->count() <= 1) {
            return response()->json([
                'message' => 'Cannot delete the last admin user.'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get active users only.
     */
    public function getActive(): JsonResponse
    {
        $users = User::with('role')->active()->get();

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Get inactive users only.
     */
    public function getInactive(): JsonResponse
    {
        $users = User::with('role')->inactive()->get();

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Get users by role.
     */
    public function getByRole($roleId): JsonResponse
    {
        $users = User::with('role')
            ->where('role_id', $roleId)
            ->paginate(10);

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Activate a user.
     */
    public function activate(User $user): JsonResponse
    {
        $user->activate();

        return response()->json([
            'data' => new UserResource($user->load('role')),
            'message' => 'User activated successfully'
        ]);
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(User $user): JsonResponse
    {
        // Prevent deactivation of the last admin user
        if ($user->isAdmin() && User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->where('is_active', true)->count() <= 1) {
            return response()->json([
                'message' => 'Cannot deactivate the last active admin user.'
            ], 422);
        }

        $user->deactivate();

        return response()->json([
            'data' => new UserResource($user->load('role')),
            'message' => 'User deactivated successfully'
        ]);
    }
}
