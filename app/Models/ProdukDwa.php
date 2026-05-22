<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'no',
    'density_water_absorption_id',
    'customer_id',
    'modelsize_id',
    'spesifikasi_id',
    'tgl_produksi',
    'sampel',
    'oven_id',
    'jam_keluar_oven_id',
    'ketebalan',
    'berat_awal',
    'berat_akhir',
    'volume',
    'density',
    'temp',
    'palm_wo',
    'palm_wa',
    'palm_water',
    'mc_wo',
    'mc_wa',
    'mc_water',
    'sl_wo',
    'sl_wa',
    'sl_water'

])]
#[Table('produk_dwa')]
class ProdukDwa extends Model
{
    public function densitywaterabsorption(): BelongsTo { return $this->belongsTo(DensityWaterAbsorption::class, 'density_water_absorption_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class, 'customer_id'); }
    public function modelSize(): BelongsTo { return $this->belongsTo(ModelSize::class, 'modelsize_id'); }
    public function spesifikasi(): BelongsTo { return $this->belongsTo(Spesifikasi::class, 'spesifikasi_id'); }
}
