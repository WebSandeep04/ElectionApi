<?php

namespace App\Http\Controllers;

use App\Http\Resources\PanchayatResource;
use App\Models\Panchayat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PanchayatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Panchayat::with(['lokSabha', 'vidhanSabha', 'block']);

        // Search by choosing or name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('panchayat_name', 'like', "%{$search}%")
                  ->orWhere('panchayat_choosing', 'like', "%{$search}%");
            });
        }

        // Optional filter by status
        if ($request->has('status')) {
            $query->where('panchayat_status', $request->get('status'));
        }

        // Sorting and pagination controls
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) $request->get('per_page', 10);
        $panchayats = $query->paginate($perPage);

        return response()->json([
            'panchayats' => PanchayatResource::collection($panchayats->items()),
            'pagination' => [
                'total' => $panchayats->total(),
                'per_page' => $panchayats->perPage(),
                'current_page' => $panchayats->currentPage(),
                'last_page' => $panchayats->lastPage(),
                'from' => $panchayats->firstItem(),
                'to' => $panchayats->lastItem(),
                'has_more_pages' => $panchayats->hasMorePages(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'loksabha_id' => ['required', 'integer', 'exists:lok_sabhas,id'],
            'vidhansabha_id' => ['required', 'integer', 'exists:vidhan_sabhas,id'],
            'block_id' => ['required', 'integer', 'exists:blocks,id'],
            'panchayat_choosing' => ['required', 'string', 'max:255'],
            'panchayat_name' => ['required', 'string', 'max:255'],
            'panchayat_status' => ['sometimes', 'string', 'max:255'],
        ]);

        // Set default status if not provided
        if (!isset($validated['panchayat_status'])) {
            $validated['panchayat_status'] = '1';
        }

        $panchayat = Panchayat::create($validated);

        return response()->json([
            'message' => 'Panchayat created successfully',
            'panchayat' => new PanchayatResource($panchayat->load(['lokSabha', 'vidhanSabha', 'block'])),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Panchayat $panchayat): JsonResponse
    {
        return response()->json([
            'panchayat' => new PanchayatResource($panchayat->load(['lokSabha', 'vidhanSabha', 'block'])),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Panchayat $panchayat): JsonResponse
    {
        $validated = $request->validate([
            'loksabha_id' => ['sometimes', 'required', 'integer', 'exists:lok_sabhas,id'],
            'vidhansabha_id' => ['sometimes', 'required', 'integer', 'exists:vidhan_sabhas,id'],
            'block_id' => ['sometimes', 'required', 'integer', 'exists:blocks,id'],
            'panchayat_choosing' => ['sometimes', 'required', 'string', 'max:255'],
            'panchayat_name' => ['sometimes', 'required', 'string', 'max:255'],
            'panchayat_status' => ['sometimes', 'string', 'max:255'],
        ]);

        $panchayat->update($validated);

        return response()->json([
            'message' => 'Panchayat updated successfully',
            'panchayat' => new PanchayatResource($panchayat->load(['lokSabha', 'vidhanSabha', 'block'])),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Panchayat $panchayat): JsonResponse
    {
        $panchayat->delete();

        return response()->json([
            'message' => 'Panchayat deleted successfully',
        ]);
    }

    /**
     * Get Panchayats by Lok Sabha ID
     */
    public function getByLokSabha(Request $request, $loksabhaId): JsonResponse
    {
        $panchayats = Panchayat::where('loksabha_id', $loksabhaId)
            ->with(['lokSabha', 'vidhanSabha', 'block'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'panchayats' => PanchayatResource::collection($panchayats->items()),
            'pagination' => [
                'total' => $panchayats->total(),
                'per_page' => $panchayats->perPage(),
                'current_page' => $panchayats->currentPage(),
                'last_page' => $panchayats->lastPage(),
                'from' => $panchayats->firstItem(),
                'to' => $panchayats->lastItem(),
                'has_more_pages' => $panchayats->hasMorePages(),
            ],
        ]);
    }

    /**
     * Get Panchayats by Vidhan Sabha ID
     */
    public function getByVidhanSabha(Request $request, $vidhansabhaId): JsonResponse
    {
        $panchayats = Panchayat::where('vidhansabha_id', $vidhansabhaId)
            ->with(['lokSabha', 'vidhanSabha', 'block'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'panchayats' => PanchayatResource::collection($panchayats->items()),
            'pagination' => [
                'total' => $panchayats->total(),
                'per_page' => $panchayats->perPage(),
                'current_page' => $panchayats->currentPage(),
                'last_page' => $panchayats->lastPage(),
                'from' => $panchayats->firstItem(),
                'to' => $panchayats->lastItem(),
                'has_more_pages' => $panchayats->hasMorePages(),
            ],
        ]);
    }

    /**
     * Get Panchayats by Block ID
     */
    public function getByBlock(Request $request, $blockId): JsonResponse
    {
        $panchayats = Panchayat::where('block_id', $blockId)
            ->with(['lokSabha', 'vidhanSabha', 'block'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'panchayats' => PanchayatResource::collection($panchayats->items()),
            'pagination' => [
                'total' => $panchayats->total(),
                'per_page' => $panchayats->perPage(),
                'current_page' => $panchayats->currentPage(),
                'last_page' => $panchayats->lastPage(),
                'from' => $panchayats->firstItem(),
                'to' => $panchayats->lastItem(),
                'has_more_pages' => $panchayats->hasMorePages(),
            ],
        ]);
    }
}
