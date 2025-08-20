<?php

namespace App\Http\Controllers;

use App\Http\Resources\VidhanSabhaResource;
use App\Models\VidhanSabha;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VidhanSabhaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = VidhanSabha::with('lokSabha');

        // Search by name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('vidhansabha_name', 'like', "%{$search}%");
        }

        // Optional filter by status (accepts generic `status`)
        if ($request->has('status')) {
            $query->where('vidhan_status', $request->get('status'));
        }

        // Sorting and pagination controls
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) $request->get('per_page', 10);
        $vidhanSabhas = $query->paginate($perPage);

        return response()->json([
            'vidhan_sabhas' => VidhanSabhaResource::collection($vidhanSabhas->items()),
            'pagination' => [
                'total' => $vidhanSabhas->total(),
                'per_page' => $vidhanSabhas->perPage(),
                'current_page' => $vidhanSabhas->currentPage(),
                'last_page' => $vidhanSabhas->lastPage(),
                'from' => $vidhanSabhas->firstItem(),
                'to' => $vidhanSabhas->lastItem(),
                'has_more_pages' => $vidhanSabhas->hasMorePages(),
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
            'vidhansabha_name' => ['required', 'string', 'max:255'],
            'vidhan_status' => ['sometimes', 'string', 'max:244'],
        ]);

        // Set default status if not provided
        if (!isset($validated['vidhan_status'])) {
            $validated['vidhan_status'] = '1';
        }

        $vidhanSabha = VidhanSabha::create($validated);

        return response()->json([
            'message' => 'Vidhan Sabha created successfully',
            'vidhan_sabha' => new VidhanSabhaResource($vidhanSabha->load('lokSabha')),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(VidhanSabha $vidhanSabha): JsonResponse
    {
        return response()->json([
            'vidhan_sabha' => new VidhanSabhaResource($vidhanSabha->load('lokSabha')),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VidhanSabha $vidhanSabha): JsonResponse
    {
        $validated = $request->validate([
            'loksabha_id' => ['sometimes', 'required', 'integer', 'exists:lok_sabhas,id'],
            'vidhansabha_name' => ['sometimes', 'required', 'string', 'max:255'],
            'vidhan_status' => ['sometimes', 'string', 'max:244'],
        ]);

        $vidhanSabha->update($validated);

        return response()->json([
            'message' => 'Vidhan Sabha updated successfully',
            'vidhan_sabha' => new VidhanSabhaResource($vidhanSabha->load('lokSabha')),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VidhanSabha $vidhanSabha): JsonResponse
    {
        $vidhanSabha->delete();

        return response()->json([
            'message' => 'Vidhan Sabha deleted successfully',
        ]);
    }

    /**
     * Get Vidhan Sabhas by Lok Sabha ID
     */
    public function getByLokSabha(Request $request, $loksabhaId): JsonResponse
    {
        $vidhanSabhas = VidhanSabha::where('loksabha_id', $loksabhaId)
            ->with('lokSabha')
            ->latest()
            ->paginate(10);

        return response()->json([
            'vidhan_sabhas' => VidhanSabhaResource::collection($vidhanSabhas->items()),
            'pagination' => [
                'total' => $vidhanSabhas->total(),
                'per_page' => $vidhanSabhas->perPage(),
                'current_page' => $vidhanSabhas->currentPage(),
                'last_page' => $vidhanSabhas->lastPage(),
                'from' => $vidhanSabhas->firstItem(),
                'to' => $vidhanSabhas->lastItem(),
                'has_more_pages' => $vidhanSabhas->hasMorePages(),
            ],
        ]);
    }
}
