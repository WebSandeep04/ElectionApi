<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokSabha extends Model
{
    use HasFactory;

    protected $table = 'lok_sabhas';

    protected $fillable = [
        'loksabha_name',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function vidhanSabhas()
    {
        return $this->hasMany(VidhanSabha::class, 'loksabha_id');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'loksabha_id');
    }

    public function panchayats()
    {
        return $this->hasMany(Panchayat::class, 'loksabha_id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'loksabha_id');
    }

    public function booths()
    {
        return $this->hasMany(Booth::class, 'loksabha_id');
    }
}
