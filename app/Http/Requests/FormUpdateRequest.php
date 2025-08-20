<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormUpdateRequest extends FormRequest
{
	public function authorize(): bool
	{
		return auth()->check();
	}

	public function rules(): array
	{
		return [
			'name' => ['required', 'string', 'min:3', 'max:255'],
			'questions' => ['required', 'array', 'min:1'],
			'questions.*.question' => ['required', 'string', 'min:3', 'max:1000'],
			'questions.*.type' => ['required', 'in:single_choice,multiple_choice,long_text'],
			'questions.*.required' => ['sometimes', 'boolean'],
			'questions.*.placeholder' => ['nullable', 'string'],
			'questions.*.options' => ['sometimes', 'array', 'min:2'],
			'questions.*.options.*' => ['string', 'min:1', 'max:500'],
		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(response()->json([
			'error' => [
				'code' => 'VALIDATION_ERROR',
				'message' => 'Validation failed',
				'details' => $validator->errors(),
			],
		], 422));
	}
}


