<?php

namespace App\Http\Controllers;

use App\Http\Resources\CasteResource;
use App\Models\Caste;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CasteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Caste::with('category');

        // Filter by category_id if provided
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by caste name if provided
        if ($request->has('caste')) {
            $query->where('caste', 'like', '%' . $request->caste . '%');
        }

        // Filter by category name if provided
        if ($request->has('category_name')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category_name . '%');
            });
        }

        $castes = $query->latest()->paginate(10);

        return response()->json([
            'castes' => CasteResource::collection($castes->items()),
            'pagination' => [
                'total' => $castes->total(),
                'per_page' => $castes->perPage(),
                'current_page' => $castes->currentPage(),
                'last_page' => $castes->lastPage(),
                'from' => $castes->firstItem(),
                'to' => $castes->lastItem(),
                'has_more_pages' => $castes->hasMorePages(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'caste' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:caste_categories,id'],
        ]);

        $caste = Caste::create($validated);
        $caste->load('category');

        return response()->json([
            'message' => 'Caste created successfully',
            'caste' => new CasteResource($caste),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Caste $caste): JsonResponse
    {
        $caste->load('category');

        return response()->json([
            'caste' => new CasteResource($caste),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Caste $caste): JsonResponse
    {
        $validated = $request->validate([
            'caste' => ['sometimes', 'required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:caste_categories,id'],
        ]);

        $caste->update($validated);
        $caste->load('category');

        return response()->json([
            'message' => 'Caste updated successfully',
            'caste' => new CasteResource($caste),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Caste $caste): JsonResponse
    {
        $caste->delete();

        return response()->json([
            'message' => 'Caste deleted successfully',
        ]);
    }

    /**
     * Get castes by category ID
     */
    public function getByCategory(string $categoryId): JsonResponse
    {
        $castes = Caste::where('category_id', $categoryId)
            ->with('category')
            ->orderBy('caste')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CasteResource::collection($castes)
        ]);
    }

    /**
     * Get castes without category (unassigned)
     */
    public function getUnassigned(): JsonResponse
    {
        $castes = Caste::whereNull('category_id')
            ->orderBy('caste')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CasteResource::collection($castes)
        ]);
    }

    /**
     * Assign caste to category
     */
    public function assignToCategory(Request $request, Caste $caste): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:caste_categories,id'],
        ]);

        $caste->update(['category_id' => $validated['category_id']]);
        $caste->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Caste assigned to category successfully',
            'caste' => new CasteResource($caste),
        ]);
    }

    /**
     * Remove caste from category (set category_id to null)
     */
    public function removeFromCategory(Caste $caste): JsonResponse
    {
        $caste->update(['category_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Caste removed from category successfully',
            'caste' => new CasteResource($caste),
        ]);
    }
}


