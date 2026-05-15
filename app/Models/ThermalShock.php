<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('thermal_shock')]
#[Fillable([
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
])]
class ThermalShock extends Model
{
    /**
     * Relasi ke data detail (anak)
     */
    public function details(): HasMany
    {
        return $this->hasMany(ThermalShockDetail::class, 'thermal_shock_id');
    }
}
