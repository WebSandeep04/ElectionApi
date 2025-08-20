<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormStoreRequest;
use App\Http\Requests\FormUpdateRequest;
use App\Http\Resources\FormResource;
use App\Models\Form;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormController extends Controller
{
	private function validateQuestionsPayload(array $questions): void
	{
		$details = [];
		foreach ($questions as $idx => $q) {
			$type = $q['type'] ?? null;
			$placeholder = $q['placeholder'] ?? null;
			$options = $q['options'] ?? null;

			if ($type === 'long_text' && $options !== null) {
				$details["questions.$idx.options"][] = 'Options are not allowed for long_text questions';
			}

			if (in_array($type, ['single_choice','multiple_choice'])) {
				if (!is_array($options)) {
					$details["questions.$idx.options"][] = 'Options must be provided as an array for choice questions';
				} else {
					$filtered = array_values(array_filter($options, fn ($v) => trim((string) $v) !== ''));
					if (count($filtered) < 2) {
						$details["questions.$idx.options"][] = 'At least two non-empty options are required';
					}
				}
			}

			if ($type !== 'long_text' && $placeholder !== null) {
				$details["questions.$idx.placeholder"][] = 'Placeholder is only allowed for long_text questions';
			}
		}

		if (!empty($details)) {
			abort(response()->json([
				'error' => [
					'code' => 'VALIDATION_ERROR',
					'message' => 'Validation failed',
					'details' => $details,
				],
			], 422));
		}
	}
	public function index(Request $request): JsonResponse
	{
		$perPage = (int) min(max((int) $request->query('per_page', 10), 1), 100);
		$query = Form::query();
		if ($search = $request->query('search')) {
			$query->where('name', 'like', "%{$search}%");
		}
		$forms = $query->latest()->paginate($perPage);
		return response()->json([
			'forms' => FormResource::collection($forms->items()),
			'pagination' => [
				'total' => $forms->total(),
				'per_page' => $forms->perPage(),
				'current_page' => $forms->currentPage(),
				'last_page' => $forms->lastPage(),
				'from' => $forms->firstItem(),
				'to' => $forms->lastItem(),
				'has_more_pages' => $forms->hasMorePages(),
			],
		]);
	}

	public function store(FormStoreRequest $request): JsonResponse
	{
		$form = null;
		$questionsPayload = $request->input('questions', []);
		$this->validateQuestionsPayload($questionsPayload);
		DB::transaction(function () use ($request, &$form, $questionsPayload) {
			$form = Form::create([
				'name' => $request->input('name'),
				'user_id' => $request->user()->id,
			]);
			$questions = $questionsPayload;
			foreach ($questions as $index => $q) {
				$question = Question::create([
					'form_id' => $form->id,
					'question_text' => $q['question'],
					'question_type' => $q['type'],
					'is_required' => (bool)($q['required'] ?? false),
					'placeholder_text' => $q['placeholder'] ?? null,
					'question_order' => $index + 1,
				]);
				if (in_array($q['type'], ['single_choice', 'multiple_choice'])) {
					$options = array_values(array_filter($q['options'] ?? [], fn ($v) => trim((string) $v) !== ''));
					foreach ($options as $optIndex => $optText) {
						QuestionOption::create([
							'question_id' => $question->id,
							'option_text' => $optText,
							'option_order' => $optIndex + 1,
						]);
					}
				}
			}
		});
		return response()->json([
			'message' => 'Form created successfully',
			'form' => new FormResource($form->load('questions.options')),
		], 201);
	}

	public function show(int $id): JsonResponse
	{
		$form = Form::with(['questions.options'])->findOrFail($id);
		return response()->json([
			'form' => new FormResource($form),
		]);
	}

	public function update(FormUpdateRequest $request, int $id): JsonResponse
	{
		$form = Form::findOrFail($id);
		$questionsPayload = $request->input('questions', []);
		$this->validateQuestionsPayload($questionsPayload);
		DB::transaction(function () use ($request, $form, $questionsPayload) {
			$form->update([
				'name' => $request->input('name'),
				'user_id' => $request->user()->id,
			]);
			// Replace questions and options
			$form->questions()->delete();
			$questions = $questionsPayload;
			foreach ($questions as $index => $q) {
				$question = Question::create([
					'form_id' => $form->id,
					'question_text' => $q['question'],
					'question_type' => $q['type'],
					'is_required' => (bool)($q['required'] ?? false),
					'placeholder_text' => $q['placeholder'] ?? null,
					'question_order' => $index + 1,
				]);
				if (in_array($q['type'], ['single_choice', 'multiple_choice'])) {
					$options = array_values(array_filter($q['options'] ?? [], fn ($v) => trim((string) $v) !== ''));
					foreach ($options as $optIndex => $optText) {
						QuestionOption::create([
							'question_id' => $question->id,
							'option_text' => $optText,
							'option_order' => $optIndex + 1,
						]);
					}
				}
			}
		});
		return response()->json([
			'message' => 'Form updated successfully',
			'form' => new FormResource($form->load('questions.options')),
		]);
	}

	public function destroy(int $id): JsonResponse
	{
		$form = Form::findOrFail($id);
		$form->delete();
		return response()->json(['message' => 'deleted']);
	}
}


