<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'thermal_shock_id',
    'kode_bakar',
    'kode_tanah',
    'oven_id',
    'customer_id',
    'modelsize_id',
    'spesifikasi_id',
    'tinggi_former_id',
    'jam_keluar_oven_id',
    'sampel',
    'berat_former',
    'tanggal_keluar_oven',
    'tgl_produksi',
    'posisi_former',
    'hasil_test',
    'suhu_actual',
    'keterangan',
])]
#[Table('produk')]

class Produk extends Model
{
    public function thermalShock(): BelongsTo { return $this->belongsTo(ThermalShock::class, 'thermal_shock_id'); }
    public function oven(): BelongsTo { return $this->belongsTo(Oven::class, 'oven_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class, 'customer_id'); }
    public function modelSize(): BelongsTo { return $this->belongsTo(ModelSize::class, 'modelsize_id'); }
    public function spesifikasi(): BelongsTo { return $this->belongsTo(Spesifikasi::class, 'spesifikasi_id'); }
    public function tinggiFormer(): BelongsTo { return $this->belongsTo(TinggiFormer::class, 'tinggi_former_id'); }
    public function jamKeluarOven(): BelongsTo { return $this->belongsTo(JamKeluarOven::class, 'jam_keluar_oven_id'); }
}
