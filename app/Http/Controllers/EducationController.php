<?php

namespace App\Http\Controllers;

use App\Http\Resources\EducationResource;
use App\Models\Education;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationController extends Controller
{
	public function index(Request $request): JsonResponse
	{
		$query = Education::query();

		if ($request->has('search')) {
			$search = $request->get('search');
			$query->where('education_name', 'like', "%{$search}%");
		}

		$sortBy = $request->get('sort_by', 'created_at');
		$sortOrder = $request->get('sort_order', 'desc');
		$query->orderBy($sortBy, $sortOrder);

		$perPage = $request->get('per_page', 10);
		$educations = $query->paginate($perPage);

		return response()->json([
			'educations' => EducationResource::collection($educations->items()),
			'pagination' => [
				'total' => $educations->total(),
				'per_page' => $educations->perPage(),
				'current_page' => $educations->currentPage(),
				'last_page' => $educations->lastPage(),
				'from' => $educations->firstItem(),
				'to' => $educations->lastItem(),
				'has_more_pages' => $educations->hasMorePages(),
			],
		]);
	}

	public function store(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'education_name' => 'required|string|max:255',
		]);

		$education = Education::create($validated);

		return response()->json([
			'data' => new EducationResource($education),
			'message' => 'Education created successfully'
		], 201);
	}

	public function show(Education $education): JsonResponse
	{
		return response()->json([
			'data' => new EducationResource($education)
		]);
	}

	public function update(Request $request, Education $education): JsonResponse
	{
		$validated = $request->validate([
			'education_name' => 'sometimes|required|string|max:255',
		]);

		$education->update($validated);

		return response()->json([
			'data' => new EducationResource($education),
			'message' => 'Education updated successfully'
		]);
	}

	public function destroy(Education $education): JsonResponse
	{
		$education->delete();

		return response()->json([
			'message' => 'Education deleted successfully'
		]);
	}
}
