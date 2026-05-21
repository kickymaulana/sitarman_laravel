<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tgl_test',
    'spec',
    'mulai_proses',
    'selesai_proses',
    'temp_air',
    'user_id',
])]
#[Table('water_absorption')]
class WaterAbsorption extends Model
{
    public function produk_wa(): HasMany
    {
        return $this->hasMany(ProdukWa::class, 'water_absorption_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
