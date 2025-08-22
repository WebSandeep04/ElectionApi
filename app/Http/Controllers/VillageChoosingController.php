<?php

namespace App\Http\Controllers;

use App\Models\VillageChoosing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VillageChoosingController extends Controller
{
    /**
     * Display a listing of village choosing options.
     */
    public function index(Request $request): JsonResponse
    {
        $query = VillageChoosing::query();

        // Optional filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Search by name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) $request->get('per_page', 10);
        $choosings = $query->paginate($perPage);

        return response()->json([
            'village_choosings' => $choosings->items(),
            'pagination' => [
                'total' => $choosings->total(),
                'per_page' => $choosings->perPage(),
                'current_page' => $choosings->currentPage(),
                'last_page' => $choosings->lastPage(),
                'from' => $choosings->firstItem(),
                'to' => $choosings->lastItem(),
                'has_more_pages' => $choosings->hasMorePages(),
            ],
        ]);
    }

    /**
     * Display the specified village choosing option.
     */
    public function show(VillageChoosing $villageChoosing): JsonResponse
    {
        return response()->json([
            'village_choosing' => $villageChoosing,
        ]);
    }

    /**
     * Get all active village choosing options (for dropdowns).
     */
    public function active(): JsonResponse
    {
        $choosings = VillageChoosing::where('status', '1')
            ->orderBy('id')
            ->get(['id', 'name']);

        return response()->json([
            'village_choosings' => $choosings,
        ]);
    }
}
