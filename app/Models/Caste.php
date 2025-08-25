<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caste extends Model
{
	use HasFactory;

	protected $fillable = [
		'caste',
		'category_id',
	];

	/**
	 * Get the category that this caste belongs to.
	 */
	public function category()
	{
		return $this->belongsTo(CasteCategory::class, 'category_id');
	}
}


