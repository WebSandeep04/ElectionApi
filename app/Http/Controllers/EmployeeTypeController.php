<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeTypeResource;
use App\Models\EmployeeType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeTypeController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): JsonResponse
	{
		$types = EmployeeType::query()->latest()->paginate(10);

		return response()->json([
			'employee_types' => EmployeeTypeResource::collection($types->items()),
			'pagination' => [
				'total' => $types->total(),
				'per_page' => $types->perPage(),
				'current_page' => $types->currentPage(),
				'last_page' => $types->lastPage(),
				'from' => $types->firstItem(),
				'to' => $types->lastItem(),
				'has_more_pages' => $types->hasMorePages(),
			],
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'type_name' => ['required', 'string', 'max:255'],
			'status' => ['sometimes', 'string', 'max:255'],
		]);

		if (!isset($validated['status'])) {
			$validated['status'] = '1';
		}

		$type = EmployeeType::create($validated);

		return response()->json([
			'message' => 'Employee type created successfully',
			'employee_type' => new EmployeeTypeResource($type),
		], 201);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(EmployeeType $employee_type): JsonResponse
	{
		return response()->json([
			'employee_type' => new EmployeeTypeResource($employee_type),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, EmployeeType $employee_type): JsonResponse
	{
		$validated = $request->validate([
			'type_name' => ['sometimes', 'required', 'string', 'max:255'],
			'status' => ['sometimes', 'string', 'max:255'],
		]);

		$employee_type->update($validated);

		return response()->json([
			'message' => 'Employee type updated successfully',
			'employee_type' => new EmployeeTypeResource($employee_type),
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(EmployeeType $employee_type): JsonResponse
	{
		$employee_type->delete();

		return response()->json([
			'message' => 'Employee type deleted successfully',
		]);
	}
}


