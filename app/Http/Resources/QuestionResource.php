<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'question' => $this->question_text,
			'type' => $this->question_type,
			'required' => (bool) $this->is_required,
			'placeholder' => $this->placeholder_text,
			'options' => $this->when(in_array($this->question_type, ['single_choice','multiple_choice']), function () {
				return $this->options->pluck('option_text')->values();
			}),
			'order' => $this->question_order,
		];
	}
}


