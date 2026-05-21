<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'no',
    'density_id',
    'customer_id',
    'modelsize_id',
    'tgl_produksi',
    'oven_id',
    'jam_keluar_oven_id',
    'ketebalan',
    'berat_awal',
    'berat_akhir',
    'volume',
    'density',
])]
#[Table('produk_density')]
class ProdukDensity extends Model
{
    public function density(): BelongsTo { return $this->belongsTo(Density::class, 'density_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class, 'customer_id'); }
    public function modelSize(): BelongsTo { return $this->belongsTo(ModelSize::class, 'modelsize_id'); }
    public function spesifikasi(): BelongsTo { return $this->belongsTo(Spesifikasi::class, 'spesifikasi_id'); }
    public function oven(): BelongsTo { return $this->belongsTo(Oven::class, 'oven_id'); }
    public function jamKeluarOven(): BelongsTo { return $this->belongsTo(JamKeluarOven::class, 'jam_keluar_oven_id'); }
}
