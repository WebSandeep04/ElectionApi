<?php

namespace App\Http\Controllers;

use App\Models\PanchayatChoosing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PanchayatChoosingController extends Controller
{
    /**
     * Display a listing of panchayat choosing options.
     */
    public function index(Request $request): JsonResponse
    {
        $query = PanchayatChoosing::query();

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
            'panchayat_choosings' => $choosings->items(),
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
     * Display the specified panchayat choosing option.
     */
    public function show(PanchayatChoosing $panchayatChoosing): JsonResponse
    {
        return response()->json([
            'panchayat_choosing' => $panchayatChoosing,
        ]);
    }

    /**
     * Get all active panchayat choosing options (for dropdowns).
     */
    public function active(): JsonResponse
    {
        $choosings = PanchayatChoosing::where('status', '1')
            ->orderBy('id')
            ->get(['id', 'name']);

        return response()->json([
            'panchayat_choosings' => $choosings,
        ]);
    }
}
