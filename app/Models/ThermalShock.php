<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('thermal_shock')]
class ThermalShock extends Model
{
    protected $fillable = [
        'thermal_oven_id',
        'thermal_pintu_id',
        'hari_tgl',
        'suhu_testing',
        'suhu_motor',
        'suhu_display',
        'suhu_actual',
        'jam_awal_proses',
        'jam_capai_suhu',
        'suhu_awal',
        'suhu_air',
        'jam_mulai_tembak',
        'jam_selesai_tembak'
    ];

    public function thermalOven(): BelongsTo
    {
        return $this->belongsTo(ThermalOven::class, 'thermal_oven_id');
    }

    public function thermalPintu(): BelongsTo
    {
        return $this->belongsTo(ThermalPintu::class, 'thermal_pintu_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ThermalShockDetail::class, 'thermal_shock_id');
    }
}
