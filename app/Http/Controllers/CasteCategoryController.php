<?php

namespace App\Http\Controllers;

use App\Http\Resources\CasteCategoryResource;
use App\Models\CasteCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class CasteCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = CasteCategory::with('castes');

        // Filter by name if provided
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $casteCategories = $query->orderBy('name')->paginate(15);
        
        return CasteCategoryResource::collection($casteCategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $casteCategory = CasteCategory::create($validator->validated());
        $casteCategory->load('castes');

        return response()->json([
            'success' => true,
            'message' => 'Caste category created successfully',
            'data' => new CasteCategoryResource($casteCategory)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $casteCategory = CasteCategory::with('castes')->find($id);

        if (!$casteCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Caste category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CasteCategoryResource($casteCategory)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $casteCategory = CasteCategory::find($id);

        if (!$casteCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Caste category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $casteCategory->update($validator->validated());
        $casteCategory->load('castes');

        return response()->json([
            'success' => true,
            'message' => 'Caste category updated successfully',
            'data' => new CasteCategoryResource($casteCategory)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $casteCategory = CasteCategory::find($id);

        if (!$casteCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Caste category not found'
            ], 404);
        }

        $casteCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Caste category deleted successfully'
        ]);
    }

    /**
     * Get castes by specific category ID
     */
    public function getCastesByCategory(string $categoryId): JsonResponse
    {
        $category = CasteCategory::with('castes')->find($categoryId);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Caste category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new CasteCategoryResource($category),
                'castes' => $category->castes
            ]
        ]);
    }
}
