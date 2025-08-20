<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		'question_id',
		'option_text',
		'option_order',
	];

	public function question(): BelongsTo
	{
		return $this->belongsTo(Question::class);
	}
}


