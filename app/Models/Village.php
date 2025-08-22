<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $table = 'villages';

    protected $fillable = [
        'loksabha_id',
        'vidhansabha_id',
        'block_id',
        'panchayat_id',
        'village_choosing_id',
        'village_choosing',
        'village_name',
        'village_status'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($village) {
            if (empty($village->village_status)) {
                $village->village_status = '1';
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

    public function villageChoosing()
    {
        return $this->belongsTo(VillageChoosing::class, 'village_choosing_id');
    }

    public function booths()
    {
        return $this->hasMany(Booth::class, 'village_id');
    }
}
