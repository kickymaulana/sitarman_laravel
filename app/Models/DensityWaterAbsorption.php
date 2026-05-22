<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'density_user_id',
    'water_absoription_user_id',
    'tgl',
    'spec',
    'mulai_proses',
    'selesai_proses',
    'temp_air'
])]
#[Table('density_water_absorption')]
class DensityWaterAbsorption extends Model
{
    public function produk_dwa(): HasMany
    {
        return $this->hasMany(ProdukDwa::class, 'density_water_absorption_id');
    }
    public function density_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'density_user_id');
    }

    public function water_absoription_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'water_absoription_user_id');
    }
}
