<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Http\Resources\VillageResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VillageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Village::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'villageChoosing']);

        // Search by choosing or name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('village_name', 'like', "%{$search}%")
                  ->orWhere('village_choosing', 'like', "%{$search}%");
            });
        }

        // Optional filter by status
        if ($request->has('status')) {
            $query->where('village_status', $request->get('status'));
        }

        // Sorting and pagination controls
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) $request->get('per_page', 10);
        $villages = $query->paginate($perPage);

        return response()->json([
            'data' => VillageResource::collection($villages),
            'pagination' => [
                'current_page' => $villages->currentPage(),
                'last_page' => $villages->lastPage(),
                'per_page' => $villages->perPage(),
                'total' => $villages->total(),
                'from' => $villages->firstItem(),
                'to' => $villages->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'loksabha_id' => 'nullable|integer|exists:lok_sabhas,id',
            'vidhansabha_id' => 'nullable|integer|exists:vidhan_sabhas,id',
            'block_id' => 'nullable|integer|exists:blocks,id',
            'panchayat_id' => 'nullable|integer|exists:panchayats,id',
            'village_choosing_id' => 'nullable|integer|exists:village_choosings,id',
            'village_choosing' => 'nullable|string|max:255',
            'village_name' => 'nullable|string|max:255',
            'village_status' => 'nullable|string|max:255',
        ]);

        $village = Village::create($validated);

        return response()->json([
            'message' => 'Village created successfully',
            'data' => new VillageResource($village->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'villageChoosing']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Village $village): JsonResponse
    {
        return response()->json([
            'data' => new VillageResource($village->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'villageChoosing']))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Village $village): JsonResponse
    {
        $validated = $request->validate([
            'loksabha_id' => 'nullable|integer|exists:lok_sabhas,id',
            'vidhansabha_id' => 'nullable|integer|exists:vidhan_sabhas,id',
            'block_id' => 'nullable|integer|exists:blocks,id',
            'panchayat_id' => 'nullable|integer|exists:panchayats,id',
            'village_choosing_id' => 'nullable|integer|exists:village_choosings,id',
            'village_choosing' => 'nullable|string|max:255',
            'village_name' => 'nullable|string|max:255',
            'village_status' => 'nullable|string|max:255',
        ]);

        $village->update($validated);

        return response()->json([
            'message' => 'Village updated successfully',
            'data' => new VillageResource($village->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'villageChoosing']))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Village $village): JsonResponse
    {
        $village->delete();

        return response()->json([
            'message' => 'Village deleted successfully'
        ]);
    }

    /**
     * Get villages by Lok Sabha ID
     */
    public function getByLokSabha(string $loksabhaId): JsonResponse
    {
        $villages = Village::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'villageChoosing'])
            ->where('loksabha_id', $loksabhaId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => VillageResource::collection($villages),
            'pagination' => [
                'current_page' => $villages->currentPage(),
                'last_page' => $villages->lastPage(),
                'per_page' => $villages->perPage(),
                'total' => $villages->total(),
                'from' => $villages->firstItem(),
                'to' => $villages->lastItem(),
            ]
        ]);
    }

    /**
     * Get villages by Vidhan Sabha ID
     */
    public function getByVidhanSabha(string $vidhansabhaId): JsonResponse
    {
        $villages = Village::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'villageChoosing'])
            ->where('vidhansabha_id', $vidhansabhaId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => VillageResource::collection($villages),
            'pagination' => [
                'current_page' => $villages->currentPage(),
                'last_page' => $villages->lastPage(),
                'per_page' => $villages->perPage(),
                'total' => $villages->total(),
                'from' => $villages->firstItem(),
                'to' => $villages->lastItem(),
            ]
        ]);
    }

    /**
     * Get villages by Block ID
     */
    public function getByBlock(string $blockId): JsonResponse
    {
        $villages = Village::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'villageChoosing'])
            ->where('block_id', $blockId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => VillageResource::collection($villages),
            'pagination' => [
                'current_page' => $villages->currentPage(),
                'last_page' => $villages->lastPage(),
                'per_page' => $villages->perPage(),
                'total' => $villages->total(),
                'from' => $villages->firstItem(),
                'to' => $villages->lastItem(),
            ]
        ]);
    }

    /**
     * Get villages by Panchayat ID
     */
    public function getByPanchayat(string $panchayatId): JsonResponse
    {
        $villages = Village::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'villageChoosing'])
            ->where('panchayat_id', $panchayatId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => VillageResource::collection($villages),
            'pagination' => [
                'current_page' => $villages->currentPage(),
                'last_page' => $villages->lastPage(),
                'per_page' => $villages->perPage(),
                'total' => $villages->total(),
                'from' => $villages->firstItem(),
                'to' => $villages->lastItem(),
            ]
        ]);
    }
}
