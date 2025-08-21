<?php

namespace App\Http\Controllers;

use App\Http\Resources\VillageDescriptionResource;
use App\Models\VillageDescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VillageDescriptionController extends Controller
{
	public function index(Request $request): JsonResponse
	{
		$query = VillageDescription::with([
			'lokSabha',
			'vidhanSabha',
			'block',
			'panchayat',
			'villageChoosing',
			'village'
		]);

		// Search by description
		if ($request->has('search')) {
			$search = $request->get('search');
			$query->where('description', 'like', "%{$search}%");
		}

		// Filter by various IDs
		if ($request->has('loksabha_id')) {
			$query->where('loksabha_id', $request->get('loksabha_id'));
		}

		if ($request->has('vidhansabha_id')) {
			$query->where('vidhansabha_id', $request->get('vidhansabha_id'));
		}

		if ($request->has('block_id')) {
			$query->where('block_id', $request->get('block_id'));
		}

		if ($request->has('panchayat_id')) {
			$query->where('panchayat_id', $request->get('panchayat_id'));
		}

		if ($request->has('village_id')) {
			$query->where('village_id', $request->get('village_id'));
		}

		// Sorting
		$sortBy = $request->get('sort_by', 'created_at');
		$sortOrder = $request->get('sort_order', 'desc');
		$query->orderBy($sortBy, $sortOrder);

		// Pagination
		$perPage = $request->get('per_page', 10);
		$descriptions = $query->paginate($perPage);

		return response()->json([
			'village_descriptions' => VillageDescriptionResource::collection($descriptions->items()),
			'pagination' => [
				'total' => $descriptions->total(),
				'per_page' => $descriptions->perPage(),
				'current_page' => $descriptions->currentPage(),
				'last_page' => $descriptions->lastPage(),
				'from' => $descriptions->firstItem(),
				'to' => $descriptions->lastItem(),
				'has_more_pages' => $descriptions->hasMorePages(),
			],
		]);
	}

	public function store(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'loksabha_id' => 'required|exists:lok_sabhas,id',
			'vidhansabha_id' => 'required|exists:vidhan_sabhas,id',
			'block_id' => 'required|exists:blocks,id',
			'panchayat_id' => 'required|exists:panchayats,id',
			'village_choosing' => 'required|exists:villages,id',
			'village_id' => 'required|exists:villages,id',
			'description' => 'required|string',
		]);

		$description = VillageDescription::create($validated);

		return response()->json([
			'data' => new VillageDescriptionResource($description->load([
				'lokSabha',
				'vidhanSabha',
				'block',
				'panchayat',
				'villageChoosing',
				'village'
			])),
			'message' => 'Village description created successfully'
		], 201);
	}

	public function show(VillageDescription $villageDescription): JsonResponse
	{
		return response()->json([
			'data' => new VillageDescriptionResource($villageDescription->load([
				'lokSabha',
				'vidhanSabha',
				'block',
				'panchayat',
				'villageChoosing',
				'village'
			]))
		]);
	}

	public function update(Request $request, VillageDescription $villageDescription): JsonResponse
	{
		$validated = $request->validate([
			'loksabha_id' => 'sometimes|required|exists:lok_sabhas,id',
			'vidhansabha_id' => 'sometimes|required|exists:vidhan_sabhas,id',
			'block_id' => 'sometimes|required|exists:blocks,id',
			'panchayat_id' => 'sometimes|required|exists:panchayats,id',
			'village_choosing' => 'sometimes|required|exists:villages,id',
			'village_id' => 'sometimes|required|exists:villages,id',
			'description' => 'sometimes|required|string',
		]);

		$villageDescription->update($validated);

		return response()->json([
			'data' => new VillageDescriptionResource($villageDescription->load([
				'lokSabha',
				'vidhanSabha',
				'block',
				'panchayat',
				'villageChoosing',
				'village'
			])),
			'message' => 'Village description updated successfully'
		]);
	}

	public function destroy(VillageDescription $villageDescription): JsonResponse
	{
		$villageDescription->delete();

		return response()->json([
			'message' => 'Village description deleted successfully'
		]);
	}
}
