<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Http\Resources\BoothResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BoothController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booth::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing']);

        // Search by name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('booth_name', 'like', "%{$search}%");
        }

        // Optional filter by status
        if ($request->has('status')) {
            $query->where('booth_status', $request->get('status'));
        }

        // Sorting and pagination controls
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) $request->get('per_page', 10);
        $booths = $query->paginate($perPage);

        return response()->json([
            'data' => BoothResource::collection($booths),
            'pagination' => [
                'current_page' => $booths->currentPage(),
                'last_page' => $booths->lastPage(),
                'per_page' => $booths->perPage(),
                'total' => $booths->total(),
                'from' => $booths->firstItem(),
                'to' => $booths->lastItem(),
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
            'panchayat_choosing' => 'nullable|string|max:255',
            'panchayat_choosing_id' => 'nullable|integer|exists:panchayat_choosings,id',
            'panchayat_id' => 'nullable|integer|exists:panchayats,id',
            'village_choosing' => 'nullable|string|max:255',
            'village_choosing_id' => 'nullable|integer|exists:village_choosings,id',
            'village_id' => 'nullable|integer|exists:villages,id',
            'booth_name' => 'nullable|string|max:255',
            'booth_status' => 'nullable|string|max:255',
        ]);

        $booth = Booth::create($validated);

        return response()->json([
            'message' => 'Booth created successfully',
            'data' => new BoothResource($booth->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booth $booth): JsonResponse
    {
        return response()->json([
            'data' => new BoothResource($booth->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing']))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booth $booth): JsonResponse
    {
        $validated = $request->validate([
            'loksabha_id' => 'nullable|integer|exists:lok_sabhas,id',
            'vidhansabha_id' => 'nullable|integer|exists:vidhan_sabhas,id',
            'block_id' => 'nullable|integer|exists:blocks,id',
            'panchayat_id' => 'nullable|integer|exists:panchayats,id',
            'panchayat_choosing' => 'nullable|string|max:255',
            'panchayat_choosing_id' => 'nullable|integer|exists:panchayat_choosings,id',
            'village_choosing' => 'nullable|string|max:255',
            'village_choosing_id' => 'nullable|integer|exists:village_choosings,id',
            'village_id' => 'nullable|integer|exists:villages,id',
            'booth_name' => 'nullable|string|max:255',
            'booth_status' => 'nullable|string|max:255',
        ]);

        $booth->update($validated);

        return response()->json([
            'message' => 'Booth updated successfully',
            'data' => new BoothResource($booth->load(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing']))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booth $booth): JsonResponse
    {
        $booth->delete();

        return response()->json([
            'message' => 'Booth deleted successfully'
        ]);
    }

    /**
     * Get booths by Lok Sabha ID
     */
    public function getByLokSabha(string $loksabhaId): JsonResponse
    {
        $booths = Booth::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing'])
            ->where('loksabha_id', $loksabhaId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => BoothResource::collection($booths),
            'pagination' => [
                'current_page' => $booths->currentPage(),
                'last_page' => $booths->lastPage(),
                'per_page' => $booths->perPage(),
                'total' => $booths->total(),
                'from' => $booths->firstItem(),
                'to' => $booths->lastItem(),
            ]
        ]);
    }

    /**
     * Get booths by Vidhan Sabha ID
     */
    public function getByVidhanSabha(string $vidhansabhaId): JsonResponse
    {
        $booths = Booth::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing'])
            ->where('vidhansabha_id', $vidhansabhaId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => BoothResource::collection($booths),
            'pagination' => [
                'current_page' => $booths->currentPage(),
                'last_page' => $booths->lastPage(),
                'per_page' => $booths->perPage(),
                'total' => $booths->total(),
                'from' => $booths->firstItem(),
                'to' => $booths->lastItem(),
            ]
        ]);
    }

    /**
     * Get booths by Block ID
     */
    public function getByBlock(string $blockId): JsonResponse
    {
        $booths = Booth::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing'])
            ->where('block_id', $blockId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => BoothResource::collection($booths),
            'pagination' => [
                'current_page' => $booths->currentPage(),
                'last_page' => $booths->lastPage(),
                'per_page' => $booths->perPage(),
                'total' => $booths->total(),
                'from' => $booths->firstItem(),
                'to' => $booths->lastItem(),
            ]
        ]);
    }

    /**
     * Get booths by Panchayat ID
     */
    public function getByPanchayat(string $panchayatId): JsonResponse
    {
        $booths = Booth::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing'])
            ->where('panchayat_id', $panchayatId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => BoothResource::collection($booths),
            'pagination' => [
                'current_page' => $booths->currentPage(),
                'last_page' => $booths->lastPage(),
                'per_page' => $booths->perPage(),
                'total' => $booths->total(),
                'from' => $booths->firstItem(),
                'to' => $booths->lastItem(),
            ]
        ]);
    }

    /**
     * Get booths by Village ID
     */
    public function getByVillage(string $villageId): JsonResponse
    {
        $booths = Booth::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing'])
            ->where('village_id', $villageId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => BoothResource::collection($booths),
            'pagination' => [
                'current_page' => $booths->currentPage(),
                'last_page' => $booths->lastPage(),
                'per_page' => $booths->perPage(),
                'total' => $booths->total(),
                'from' => $booths->firstItem(),
                'to' => $booths->lastItem(),
            ]
        ]);
    }

    /**
     * Get booths by Panchayat Choosing ID
     */
    public function getByPanchayatChoosing(string $panchayatChoosingId): JsonResponse
    {
        $booths = Booth::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing'])
            ->where('panchayat_choosing_id', $panchayatChoosingId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => BoothResource::collection($booths),
            'pagination' => [
                'current_page' => $booths->currentPage(),
                'last_page' => $booths->lastPage(),
                'per_page' => $booths->perPage(),
                'total' => $booths->total(),
                'from' => $booths->firstItem(),
                'to' => $booths->lastItem(),
            ]
        ]);
    }

    /**
     * Get booths by Village Choosing ID
     */
    public function getByVillageChoosing(string $villageChoosingId): JsonResponse
    {
        $booths = Booth::with(['lokSabha', 'vidhanSabha', 'block', 'panchayat', 'village', 'panchayatChoosing', 'villageChoosing'])
            ->where('village_choosing_id', $villageChoosingId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => BoothResource::collection($booths),
            'pagination' => [
                'current_page' => $booths->currentPage(),
                'last_page' => $booths->lastPage(),
                'per_page' => $booths->perPage(),
                'total' => $booths->total(),
                'from' => $booths->firstItem(),
                'to' => $booths->lastItem(),
            ]
        ]);
    }
}
