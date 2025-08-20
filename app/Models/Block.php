<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $table = 'blocks';

    protected $fillable = [
        'loksabha_id',
        'vidhansabha_id',
        'block_name',
        'block_status',
    ];

    protected $casts = [
        'block_status' => 'string',
    ];

    /**
     * Get the Lok Sabha that owns this Block
     */
    public function lokSabha()
    {
        return $this->belongsTo(LokSabha::class, 'loksabha_id');
    }

    /**
     * Get the Vidhan Sabha that owns this Block
     */
    public function vidhanSabha()
    {
        return $this->belongsTo(VidhanSabha::class, 'vidhansabha_id');
    }

    public function panchayats()
    {
        return $this->hasMany(Panchayat::class, 'block_id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'block_id');
    }

    public function booths()
    {
        return $this->hasMany(Booth::class, 'block_id');
    }
}
