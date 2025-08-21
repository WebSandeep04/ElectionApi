<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CastRatio extends Model
{
    use HasFactory;

    protected $primaryKey = 'caste_ratio_id';

    protected $fillable = [
        'loksabha_id',
        'vidhansabha_id',
        'block_id',
        'panchayat_id',
        'village_choosing',
        'village_id',
        'booth_id',
        'caste_id',
        'caste_ratio',
    ];

    protected $casts = [
        'caste_ratio' => 'integer',
    ];

    /**
     * Get the lok sabha that owns the cast ratio
     */
    public function lokSabha(): BelongsTo
    {
        return $this->belongsTo(LokSabha::class, 'loksabha_id');
    }

    /**
     * Get the vidhan sabha that owns the cast ratio
     */
    public function vidhanSabha(): BelongsTo
    {
        return $this->belongsTo(VidhanSabha::class, 'vidhansabha_id');
    }

    /**
     * Get the block that owns the cast ratio
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    /**
     * Get the panchayat that owns the cast ratio
     */
    public function panchayat(): BelongsTo
    {
        return $this->belongsTo(Panchayat::class, 'panchayat_id');
    }

    /**
     * Get the village choosing that owns the cast ratio
     */
    public function villageChoosing(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_choosing');
    }

    /**
     * Get the village that owns the cast ratio
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    /**
     * Get the booth that owns the cast ratio
     */
    public function booth(): BelongsTo
    {
        return $this->belongsTo(Booth::class, 'booth_id');
    }

    /**
     * Get the caste that owns the cast ratio
     */
    public function caste(): BelongsTo
    {
        return $this->belongsTo(Caste::class, 'caste_id');
    }
}
