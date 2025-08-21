<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageDescription extends Model
{
	use HasFactory;

	protected $fillable = [
		'loksabha_id',
		'vidhansabha_id',
		'block_id',
		'panchayat_id',
		'village_choosing',
		'village_id',
		'description',
	];

	public function lokSabha(): BelongsTo
	{
		return $this->belongsTo(LokSabha::class, 'loksabha_id');
	}

	public function vidhanSabha(): BelongsTo
	{
		return $this->belongsTo(VidhanSabha::class, 'vidhansabha_id');
	}

	public function block(): BelongsTo
	{
		return $this->belongsTo(Block::class, 'block_id');
	}

	public function panchayat(): BelongsTo
	{
		return $this->belongsTo(Panchayat::class, 'panchayat_id');
	}

	public function villageChoosing(): BelongsTo
	{
		return $this->belongsTo(Village::class, 'village_choosing');
	}

	public function village(): BelongsTo
	{
		return $this->belongsTo(Village::class, 'village_id');
	}
}
