<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanchayatChoosing extends Model
{
    use HasFactory;

    protected $table = 'panchayat_choosings';

    protected $fillable = [
        'name',
        'status',
    ];

    public function panchayats()
    {
        return $this->hasMany(Panchayat::class, 'panchayat_choosing_id');
    }
}


