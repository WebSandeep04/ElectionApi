<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
	/**
	 * List permissions with search, filter, sort, paginate
	 */
	public function index(Request $request): JsonResponse
	{
		$query = Permission::query();

		if ($request->has('search')) {
			$search = $request->get('search');
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('display_name', 'like', "%{$search}%");
			});
		}

		if ($request->has('is_active')) {
			$query->where('is_active', $request->boolean('is_active'));
		}

		$sortBy = $request->get('sort_by', 'created_at');
		$sortOrder = $request->get('sort_order', 'desc');
		$query->orderBy($sortBy, $sortOrder);

		$perPage = (int) $request->get('per_page', 100);
		$permissions = $query->paginate($perPage);

		return response()->json([
			'data' => $permissions->items(),
			'meta' => [
				'current_page' => $permissions->currentPage(),
				'last_page' => $permissions->lastPage(),
				'per_page' => $permissions->perPage(),
				'total' => $permissions->total(),
			],
		]);
	}

	public function store(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255|unique:permissions,name',
			'display_name' => 'required|string|max:255',
			'description' => 'nullable|string',
			'is_active' => 'sometimes|boolean',
		]);

		$permission = Permission::create($validated);

		return response()->json([
			'data' => $permission,
			'message' => 'Permission created successfully',
		], 201);
	}

	public function show(Permission $permission): JsonResponse
	{
		return response()->json([
			'data' => $permission,
		]);
	}

	public function update(Request $request, Permission $permission): JsonResponse
	{
		$validated = $request->validate([
			'name' => 'sometimes|string|max:255|unique:permissions,name,' . $permission->id,
			'display_name' => 'sometimes|string|max:255',
			'description' => 'nullable|string',
			'is_active' => 'sometimes|boolean',
		]);

		$permission->update($validated);

		return response()->json([
			'data' => $permission,
			'message' => 'Permission updated successfully',
		]);
	}

	public function destroy(Permission $permission): JsonResponse
	{
		$permission->delete();

		return response()->json([
			'message' => 'Permission deleted successfully',
		]);
	}
}


