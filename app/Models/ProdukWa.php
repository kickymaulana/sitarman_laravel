<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'water_absorption_id',
    'tgl_produksi',
    'customer_id',
    'modelsize_id',
    'spesifikasi_id',
    'sampel',
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
#[Table('produk_wa')]
class ProdukWa extends Model
{
    public function waterabsorption(): BelongsTo { return $this->belongsTo(WaterAbsorption::class, 'water_absorption_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class, 'customer_id'); }
    public function modelSize(): BelongsTo { return $this->belongsTo(ModelSize::class, 'modelsize_id'); }
    public function spesifikasi(): BelongsTo { return $this->belongsTo(Spesifikasi::class, 'spesifikasi_id'); }
}
