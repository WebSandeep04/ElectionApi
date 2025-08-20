<?php

namespace App\Http\Controllers;

use App\Http\Resources\LokSabhaResource;
use App\Models\LokSabha;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LokSabhaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = LokSabha::query();

        // Search by name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('loksabha_name', 'like', "%{$search}%");
        }

        // Optional filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Sorting and pagination controls
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) $request->get('per_page', 10);
        $lokSabhas = $query->paginate($perPage);

        return response()->json([
            'lok_sabhas' => LokSabhaResource::collection($lokSabhas->items()),
            'pagination' => [
                'total' => $lokSabhas->total(),
                'per_page' => $lokSabhas->perPage(),
                'current_page' => $lokSabhas->currentPage(),
                'last_page' => $lokSabhas->lastPage(),
                'from' => $lokSabhas->firstItem(),
                'to' => $lokSabhas->lastItem(),
                'has_more_pages' => $lokSabhas->hasMorePages(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'loksabha_name' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:255'],
        ]);

        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = '1';
        }

        $lokSabha = LokSabha::create($validated);

        return response()->json([
            'message' => 'Lok Sabha created successfully',
            'lok_sabha' => new LokSabhaResource($lokSabha),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(LokSabha $lokSabha): JsonResponse
    {
        return response()->json([
            'lok_sabha' => new LokSabhaResource($lokSabha),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LokSabha $lokSabha): JsonResponse
    {
        $validated = $request->validate([
            'loksabha_name' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:255'],
        ]);

        $lokSabha->update($validated);

        return response()->json([
            'message' => 'Lok Sabha updated successfully',
            'lok_sabha' => new LokSabhaResource($lokSabha),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LokSabha $lokSabha): JsonResponse
    {
        $lokSabha->delete();

        return response()->json([
            'message' => 'Lok Sabha deleted successfully',
        ]);
    }
}
