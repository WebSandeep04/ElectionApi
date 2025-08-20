<?php

namespace App\Http\Controllers;

use App\Http\Resources\CasteResource;
use App\Models\Caste;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CasteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $castes = Caste::query()->latest()->paginate(10);

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
        ]);

        $caste = Caste::create($validated);

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
            'caste' => ['required', 'string', 'max:255'],
        ]);

        $caste->update($validated);

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
}


