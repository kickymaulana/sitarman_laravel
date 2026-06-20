<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('thermal_shock')]
#[Fillable([
    'user_id',
    'thermal_oven_id',
    'thermal_pintu_id',
    'hari_tgl',
    'suhu_testing',
    'suhu_display',
    'suhu_actual',
    'jam_awal_proses',
    'jam_capai_suhu',
    'suhu_awal',
    'suhu_air',
    'jam_mulai_tembak',
    'jam_selesai_tembak',

    // Tambahan field dari tabel produk lama
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
    'hasil_test_180',
    'hasil_test_200',
    'keterangan',
])]
class ThermalShock extends Model
{
    // === Relasi bawaan Thermal Shock Asli ===
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thermalOven(): BelongsTo
    {
        return $this->belongsTo(ThermalOven::class, 'thermal_oven_id');
    }

    public function thermalPintu(): BelongsTo
    {
        return $this->belongsTo(ThermalPintu::class, 'thermal_pintu_id');
    }

    // === Relasi tambahan dari pindahan Produk ===
    public function oven(): BelongsTo
    {
        return $this->belongsTo(Oven::class, 'oven_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function modelSize(): BelongsTo
    {
        return $this->belongsTo(ModelSize::class, 'modelsize_id');
    }

    public function spesifikasi(): BelongsTo
    {
        return $this->belongsTo(Spesifikasi::class, 'spesifikasi_id');
    }

    public function tinggiFormer(): BelongsTo
    {
        return $this->belongsTo(TinggiFormer::class, 'tinggi_former_id');
    }

    public function jamKeluarOven(): BelongsTo
    {
        return $this->belongsTo(JamKeluarOven::class, 'jam_keluar_oven_id');
    }
}
