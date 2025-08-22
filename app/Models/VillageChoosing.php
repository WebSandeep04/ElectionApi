<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillageChoosing extends Model
{
    use HasFactory;

    protected $table = 'village_choosings';

    protected $fillable = [
        'name',
        'status',
    ];

    public function villages()
    {
        return $this->hasMany(Village::class, 'village_choosing_id');
    }
}


