<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CasteCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the castes that belong to this category.
     */
    public function castes()
    {
        return $this->hasMany(Caste::class, 'category_id');
    }
}
