<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlockResource;
use App\Models\Block;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Block::with(['lokSabha', 'vidhanSabha']);

        // Search by name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('block_name', 'like', "%{$search}%");
        }

        // Optional filter by status
        if ($request->has('status')) {
            $query->where('block_status', $request->get('status'));
        }

        // Sorting and pagination controls
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) $request->get('per_page', 10);
        $blocks = $query->paginate($perPage);

        return response()->json([
            'blocks' => BlockResource::collection($blocks->items()),
            'pagination' => [
                'total' => $blocks->total(),
                'per_page' => $blocks->perPage(),
                'current_page' => $blocks->currentPage(),
                'last_page' => $blocks->lastPage(),
                'from' => $blocks->firstItem(),
                'to' => $blocks->lastItem(),
                'has_more_pages' => $blocks->hasMorePages(),
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
            'block_name' => ['required', 'string', 'max:255'],
            'block_status' => ['sometimes', 'string', 'max:255'],
        ]);

        // Set default status if not provided
        if (!isset($validated['block_status'])) {
            $validated['block_status'] = '1';
        }

        $block = Block::create($validated);

        return response()->json([
            'message' => 'Block created successfully',
            'block' => new BlockResource($block->load(['lokSabha', 'vidhanSabha'])),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Block $block): JsonResponse
    {
        return response()->json([
            'block' => new BlockResource($block->load(['lokSabha', 'vidhanSabha'])),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Block $block): JsonResponse
    {
        $validated = $request->validate([
            'loksabha_id' => ['sometimes', 'required', 'integer', 'exists:lok_sabhas,id'],
            'vidhansabha_id' => ['sometimes', 'required', 'integer', 'exists:vidhan_sabhas,id'],
            'block_name' => ['sometimes', 'required', 'string', 'max:255'],
            'block_status' => ['sometimes', 'string', 'max:255'],
        ]);

        $block->update($validated);

        return response()->json([
            'message' => 'Block updated successfully',
            'block' => new BlockResource($block->load(['lokSabha', 'vidhanSabha'])),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Block $block): JsonResponse
    {
        $block->delete();

        return response()->json([
            'message' => 'Block deleted successfully',
        ]);
    }

    /**
     * Get Blocks by Lok Sabha ID
     */
    public function getByLokSabha(Request $request, $loksabhaId): JsonResponse
    {
        $blocks = Block::where('loksabha_id', $loksabhaId)
            ->with(['lokSabha', 'vidhanSabha'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'blocks' => BlockResource::collection($blocks->items()),
            'pagination' => [
                'total' => $blocks->total(),
                'per_page' => $blocks->perPage(),
                'current_page' => $blocks->currentPage(),
                'last_page' => $blocks->lastPage(),
                'from' => $blocks->firstItem(),
                'to' => $blocks->lastItem(),
                'has_more_pages' => $blocks->hasMorePages(),
            ],
        ]);
    }

    /**
     * Get Blocks by Vidhan Sabha ID
     */
    public function getByVidhanSabha(Request $request, $vidhansabhaId): JsonResponse
    {
        $blocks = Block::where('vidhansabha_id', $vidhansabhaId)
            ->with(['lokSabha', 'vidhanSabha'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'blocks' => BlockResource::collection($blocks->items()),
            'pagination' => [
                'total' => $blocks->total(),
                'per_page' => $blocks->perPage(),
                'current_page' => $blocks->currentPage(),
                'last_page' => $blocks->lastPage(),
                'from' => $blocks->firstItem(),
                'to' => $blocks->lastItem(),
                'has_more_pages' => $blocks->hasMorePages(),
            ],
        ]);
    }
}
