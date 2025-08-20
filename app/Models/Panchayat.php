<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panchayat extends Model
{
    use HasFactory;

    protected $table = 'panchayats';

    protected $fillable = [
        'loksabha_id',
        'vidhansabha_id',
        'block_id',
        'panchayat_choosing',
        'panchayat_name',
        'panchayat_status',
    ];

    protected $casts = [
        'panchayat_status' => 'string',
    ];

    /**
     * Get the Lok Sabha that owns this Panchayat
     */
    public function lokSabha()
    {
        return $this->belongsTo(LokSabha::class, 'loksabha_id');
    }

    /**
     * Get the Vidhan Sabha that owns this Panchayat
     */
    public function vidhanSabha()
    {
        return $this->belongsTo(VidhanSabha::class, 'vidhansabha_id');
    }

    /**
     * Get the Block that owns this Panchayat
     */
    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'panchayat_id');
    }

    public function booths()
    {
        return $this->hasMany(Booth::class, 'panchayat_id');
    }
}
