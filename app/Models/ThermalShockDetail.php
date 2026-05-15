<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('thermal_shock_detail')]
#[Fillable([
    'thermal_shock_id',
    'customer_id', // Ganti dari 'customer'
    'modelsize_id', // Ganti dari 'model_size'
    'oven_kode_tanah',
    'spesifikasi',
    'berat_former',
    'tanggal_keluar_oven',
    'tgl_produksi',
    'posisi_former',
    'hasil_test',
    'keterangan',
    'paraf_qc'
])]
class ThermalShockDetail extends Model
{
    public function thermalShock(): BelongsTo
    {
        return $this->belongsTo(ThermalShock::class, 'thermal_shock_id');
    }

    // Relasi ke Model Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Relasi ke Model ModelSize
    public function modelSize(): BelongsTo
    {
        return $this->belongsTo(ModelSize::class, 'modelsize_id');
    }
}
