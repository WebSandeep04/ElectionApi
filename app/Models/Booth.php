<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booth extends Model
{
    use HasFactory;

    protected $table = 'booths';

    protected $fillable = [
        'loksabha_id',
        'vidhansabha_id',
        'block_id',
        'panchayat_id',
        'panchayat_choosing_id',
        'village_choosing',
        'village_choosing_id',
        'village_id',
        'booth_name',
        'booth_status'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booth) {
            if (empty($booth->booth_status)) {
                $booth->booth_status = '1';
            }
        });
    }

    public function lokSabha()
    {
        return $this->belongsTo(LokSabha::class, 'loksabha_id');
    }

    public function vidhanSabha()
    {
        return $this->belongsTo(VidhanSabha::class, 'vidhansabha_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function panchayat()
    {
        return $this->belongsTo(Panchayat::class, 'panchayat_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function panchayatChoosing()
    {
        return $this->belongsTo(PanchayatChoosing::class, 'panchayat_choosing_id');
    }

    public function villageChoosing()
    {
        return $this->belongsTo(VillageChoosing::class, 'village_choosing_id');
    }
}
