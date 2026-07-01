<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('thermal_shock')]
#[Fillable([
  'user_id',
    'thermal_pintu_id',
    'hari_tgl',

    // Parameter Pengujian 180°C
    'suhu_awal_180',
    'suhu_display_180',
    'suhu_actual_180',
    'suhu_air_180',
    'jam_awal_proses_180',
    'jam_capai_suhu_180',
    'jam_mulai_tembak_180',
    'jam_selesai_tembak_180',

    // Parameter Pengujian 200°C
    'suhu_awal_200',
    'suhu_display_200',
    'suhu_actual_200',
    'suhu_air_200',
    'jam_awal_proses_200',
    'jam_capai_suhu_200',
    'jam_mulai_tembak_200',
    'jam_selesai_tembak_200',

    // Data Manufaktur Produk
    'kode_bakar',
    'kode_tanah',
    'oven_id',
    'customer_id',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thermalPintu(): BelongsTo
    {
        return $this->belongsTo(ThermalPintu::class, 'thermal_pintu_id');
    }

    public function oven(): BelongsTo
    {
        return $this->belongsTo(Oven::class, 'oven_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
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
