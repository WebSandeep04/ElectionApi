<?php

namespace App\Http\Controllers;

use App\Http\Resources\CastRatioResource;
use App\Models\CastRatio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CastRatioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CastRatio::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'panchayatChoosing', 'villageChoosing', 'village', 'booth', 'caste', 'category']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('caste', function ($q) use ($search) {
                $q->where('caste', 'like', "%{$search}%");
            });
        }

        // Filter by caste_id
        if ($request->has('caste_id')) {
            $query->where('caste_id', $request->caste_id);
        }

        // Filter by category_id
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by category name
        if ($request->has('category_name')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category_name . '%');
            });
        }

        // Filter by loksabha_id
        if ($request->has('loksabha_id')) {
            $query->where('loksabha_id', $request->loksabha_id);
        }

        // Filter by vidhansabha_id
        if ($request->has('vidhansabha_id')) {
            $query->where('vidhansabha_id', $request->vidhansabha_id);
        }

        // Filter by block_id
        if ($request->has('block_id')) {
            $query->where('block_id', $request->block_id);
        }

        // Filter by panchayat_id
        if ($request->has('panchayat_id')) {
            $query->where('panchayat_id', $request->panchayat_id);
        }

        // Filter by panchayat_choosing_id
        if ($request->has('panchayat_choosing_id')) {
            $query->where('panchayat_choosing_id', $request->panchayat_choosing_id);
        }

        // Filter by village_id
        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        // Filter by village_choosing_id
        if ($request->has('village_choosing_id')) {
            $query->where('village_choosing_id', $request->village_choosing_id);
        }

        // Filter by booth_id
        if ($request->has('booth_id')) {
            $query->where('booth_id', $request->booth_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $castRatios = $query->paginate($perPage);

        // Map the collection to add 'village_choosing_label'
        $castRatiosCollection = collect($castRatios->items())->map(function ($item) {
            $itemArray = (new CastRatioResource($item))->toArray(request());
            // Add the label based on village_choosing value
            if (isset($item->village_choosing)) {
                if ($item->village_choosing == 1) {
                    $itemArray['village_choosing_label'] = 'Ward';
                } elseif ($item->village_choosing == 2) {
                    $itemArray['village_choosing_label'] = 'Village';
                } else {
                    $itemArray['village_choosing_label'] = null;
                }
            } else {
                $itemArray['village_choosing_label'] = null;
            }
            return $itemArray;
        });

        return response()->json([
            'cast_ratios' => $castRatiosCollection,
            'pagination' => [
                'total' => $castRatios->total(),
                'per_page' => $castRatios->perPage(),
                'current_page' => $castRatios->currentPage(),
                'last_page' => $castRatios->lastPage(),
                'from' => $castRatios->firstItem(),
                'to' => $castRatios->lastItem(),
                'has_more_pages' => $castRatios->hasMorePages(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'loksabha_id' => 'nullable|exists:lok_sabhas,id',
            'vidhansabha_id' => 'nullable|exists:vidhan_sabhas,id',
            'block_id' => 'nullable|exists:blocks,id',
            'panchayat_choosing_id' => 'nullable|exists:panchayat_choosings,id',
            'panchayat_id' => 'nullable|exists:panchayats,id',
            'village_choosing_id' => 'nullable|exists:village_choosings,id',
            'village_id' => 'nullable|exists:villages,id',
            'booth_id' => 'nullable|exists:booths,id',
            'caste_id' => 'required|exists:castes,id',
            'category_id' => 'nullable|exists:caste_categories,id',
            'caste_ratio' => 'required|integer|min:0|max:100',
        ]);

        $castRatio = CastRatio::create($request->all());

        return response()->json([
            'data' => new CastRatioResource($castRatio->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'panchayatChoosing', 'villageChoosing', 'village', 'booth', 'caste', 'category'])),
            'message' => 'Cast ratio created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CastRatio $castRatio): JsonResponse
    {
        return response()->json([
            'data' => new CastRatioResource($castRatio->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'panchayatChoosing', 'villageChoosing', 'village', 'booth', 'caste', 'category']))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CastRatio $castRatio): JsonResponse
    {
        $request->validate([
            'loksabha_id' => 'nullable|exists:lok_sabhas,id',
            'vidhansabha_id' => 'nullable|exists:vidhan_sabhas,id',
            'block_id' => 'nullable|exists:blocks,id',
            'panchayat_choosing_id' => 'nullable|exists:panchayat_choosings,id',
            'panchayat_id' => 'nullable|exists:panchayats,id',
            'village_choosing_id' => 'nullable|exists:village_choosings,id',
            'village_id' => 'nullable|exists:villages,id',
            'booth_id' => 'nullable|exists:booths,id',
            'caste_id' => 'sometimes|required|exists:castes,id',
            'category_id' => 'nullable|exists:caste_categories,id',
            'caste_ratio' => 'sometimes|required|integer|min:0|max:100',
        ]);

        $castRatio->update($request->all());

        return response()->json([
            'data' => new CastRatioResource($castRatio->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'panchayatChoosing', 'villageChoosing', 'village', 'booth', 'caste', 'category'])),
            'message' => 'Cast ratio updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CastRatio $castRatio): JsonResponse
    {
        $castRatio->delete();

        return response()->json([
            'message' => 'Cast ratio deleted successfully'
        ]);
    }

    /**
     * Get cast ratios by Panchayat Choosing ID
     */
    public function getByPanchayatChoosing(string $panchayatChoosingId): JsonResponse
    {
        $castRatios = CastRatio::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'panchayatChoosing', 'villageChoosing', 'village', 'booth', 'caste', 'category'])
            ->where('panchayat_choosing_id', $panchayatChoosingId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => CastRatioResource::collection($castRatios),
            'pagination' => [
                'current_page' => $castRatios->currentPage(),
                'last_page' => $castRatios->lastPage(),
                'per_page' => $castRatios->perPage(),
                'total' => $castRatios->total(),
                'from' => $castRatios->firstItem(),
                'to' => $castRatios->lastItem(),
            ]
        ]);
    }

    /**
     * Get cast ratios by Village Choosing ID
     */
    public function getByVillageChoosing(string $villageChoosingId): JsonResponse
    {
        $castRatios = CastRatio::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'panchayatChoosing', 'villageChoosing', 'village', 'booth', 'caste', 'category'])
            ->where('village_choosing_id', $villageChoosingId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => CastRatioResource::collection($castRatios),
            'pagination' => [
                'current_page' => $castRatios->currentPage(),
                'last_page' => $castRatios->lastPage(),
                'per_page' => $castRatios->perPage(),
                'total' => $castRatios->total(),
                'from' => $castRatios->firstItem(),
                'to' => $castRatios->lastItem(),
            ]
        ]);
    }

    /**
     * Get cast ratios by category ID
     */
    public function getByCategory(string $categoryId): JsonResponse
    {
        $castRatios = CastRatio::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'panchayatChoosing', 'villageChoosing', 'village', 'booth', 'caste', 'category'])
            ->where('category_id', $categoryId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => CastRatioResource::collection($castRatios),
            'pagination' => [
                'current_page' => $castRatios->currentPage(),
                'last_page' => $castRatios->lastPage(),
                'per_page' => $castRatios->perPage(),
                'total' => $castRatios->total(),
                'from' => $castRatios->firstItem(),
                'to' => $castRatios->lastItem(),
            ]
        ]);
    }

    /**
     * Get cast ratios without category (unassigned)
     */
    public function getUnassigned(): JsonResponse
    {
        $castRatios = CastRatio::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'panchayatChoosing', 'villageChoosing', 'village', 'booth', 'caste', 'category'])
            ->whereNull('category_id')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => CastRatioResource::collection($castRatios),
            'pagination' => [
                'current_page' => $castRatios->currentPage(),
                'last_page' => $castRatios->lastPage(),
                'per_page' => $castRatios->perPage(),
                'total' => $castRatios->total(),
                'from' => $castRatios->firstItem(),
                'to' => $castRatios->lastItem(),
            ]
        ]);
    }

    /**
     * Assign cast ratio to category
     */
    public function assignToCategory(Request $request, CastRatio $castRatio): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:caste_categories,id'],
        ]);

        $castRatio->update(['category_id' => $validated['category_id']]);
        $castRatio->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'panchayatChoosing', 'villageChoosing', 'village', 'booth', 'caste', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Cast ratio assigned to category successfully',
            'data' => new CastRatioResource($castRatio),
        ]);
    }

    /**
     * Remove cast ratio from category (set category_id to null)
     */
    public function removeFromCategory(CastRatio $castRatio): JsonResponse
    {
        $castRatio->update(['category_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Cast ratio removed from category successfully',
            'data' => new CastRatioResource($castRatio),
        ]);
    }
}
