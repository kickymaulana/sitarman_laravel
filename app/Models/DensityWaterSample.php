<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tgl_test',
    'urutan',
    'kode_sample',
    'no_open',
    'ketebalan',
    'berat_awal',
    'berat_akhir',
    'volume',
    'density',
    'perlakuan',
    'palm_wo',
    'palm_wa',
    'palm_water',
    'mc_wo',
    'mc_wa',
    'mc_water',
    'sl_wo',
    'sl_wa',
    'sl_water',
    'spec',
    'mulai_proses',
    'selesai_proses',
    'temp_air',
    'status',
    'created_by',
    'completed_by',
])]
#[Table('density_water_sample')]
class DensityWaterSample extends Model
{
    protected function casts(): array
    {
        return [
            'tgl_test' => 'date:Y-m-d',
            'ketebalan' => 'decimal:2',
            'berat_awal' => 'decimal:2',
            'berat_akhir' => 'decimal:2',
            'volume' => 'decimal:2',
            'density' => 'decimal:2',
            'palm_wo' => 'decimal:3',
            'palm_wa' => 'decimal:3',
            'palm_water' => 'decimal:3',
            'mc_wo' => 'decimal:3',
            'mc_wa' => 'decimal:3',
            'mc_water' => 'decimal:3',
            'sl_wo' => 'decimal:3',
            'sl_wa' => 'decimal:3',
            'sl_water' => 'decimal:3',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
