<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VidhanSabha extends Model
{
    use HasFactory;

    protected $table = 'vidhan_sabhas';

    protected $fillable = [
        'loksabha_id',
        'vidhansabha_name',
        'vidhan_status',
    ];

    protected $casts = [
        'vidhan_status' => 'string',
    ];

    /**
     * Get the Lok Sabha that owns this Vidhan Sabha
     */
    public function lokSabha()
    {
        return $this->belongsTo(LokSabha::class, 'loksabha_id');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'vidhansabha_id');
    }

    public function panchayats()
    {
        return $this->hasMany(Panchayat::class, 'vidhansabha_id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'vidhansabha_id');
    }

    public function booths()
    {
        return $this->hasMany(Booth::class, 'vidhansabha_id');
    }
}
