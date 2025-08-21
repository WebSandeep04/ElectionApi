<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
	public function index(Request $request): JsonResponse
	{
		$query = ExpenseCategory::query();

		if ($request->has('search')) {
			$search = $request->get('search');
			$query->where('category', 'like', "%{$search}%");
		}

		$sortBy = $request->get('sort_by', 'created_at');
		$sortOrder = $request->get('sort_order', 'desc');
		$query->orderBy($sortBy, $sortOrder);

		$perPage = $request->get('per_page', 10);
		$categories = $query->paginate($perPage);

		return response()->json([
			'expense_categories' => ExpenseCategoryResource::collection($categories->items()),
			'pagination' => [
				'total' => $categories->total(),
				'per_page' => $categories->perPage(),
				'current_page' => $categories->currentPage(),
				'last_page' => $categories->lastPage(),
				'from' => $categories->firstItem(),
				'to' => $categories->lastItem(),
				'has_more_pages' => $categories->hasMorePages(),
			],
		]);
	}

	public function store(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'category' => 'required|string|max:255',
		]);

		$category = ExpenseCategory::create($validated);

		return response()->json([
			'data' => new ExpenseCategoryResource($category),
			'message' => 'Expense category created successfully'
		], 201);
	}

	public function show(ExpenseCategory $expenseCategory): JsonResponse
	{
		return response()->json([
			'data' => new ExpenseCategoryResource($expenseCategory)
		]);
	}

	public function update(Request $request, ExpenseCategory $expenseCategory): JsonResponse
	{
		$validated = $request->validate([
			'category' => 'sometimes|required|string|max:255',
		]);

		$expenseCategory->update($validated);

		return response()->json([
			'data' => new ExpenseCategoryResource($expenseCategory),
			'message' => 'Expense category updated successfully'
		]);
	}

	public function destroy(ExpenseCategory $expenseCategory): JsonResponse
	{
		$expenseCategory->delete();

		return response()->json([
			'message' => 'Expense category deleted successfully'
		]);
	}
}
