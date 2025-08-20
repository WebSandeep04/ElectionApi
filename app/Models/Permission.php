<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'display_name',
		'description',
		'is_active',
	];

	protected $casts = [
		'is_active' => 'boolean',
	];

	/**
	 * Roles that have this permission.
	 */
	public function roles(): BelongsToMany
	{
		return $this->belongsToMany(Role::class, 'permission_role');
	}
}


