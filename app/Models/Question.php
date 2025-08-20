<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
	use HasFactory;

	protected $fillable = [
		'form_id',
		'question_text',
		'question_type',
		'is_required',
		'placeholder_text',
		'question_order',
	];

	protected $casts = [
		'is_required' => 'boolean',
	];

	public function form(): BelongsTo
	{
		return $this->belongsTo(Form::class);
	}

	public function options(): HasMany
	{
		return $this->hasMany(QuestionOption::class)->orderBy('option_order');
	}
}


