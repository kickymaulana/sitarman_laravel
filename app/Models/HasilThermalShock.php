<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tanggal_keluar_oven',
    'modelsize',
    'oven_id',
    'jam_keluar_oven_id',
    'customer_id',
    'modelsize_id',
    'tinggi_former_id',
    'spesifikasi_id',
    'kode_tanah',
    'suhu_180',
    'suhu_200',
    'suhu',
    'berat_former',
    'thickness',
    'chemical',
    'wa_palm',
    'wa_mc',
    'wa_sli',
    'density',
    'luas_area',
    'visual'
])]
#[Table('hasil_thermalshock')]
class HasilThermalShock extends Model
{
    public function oven(): BelongsTo { return $this->belongsTo(Oven::class, 'oven_id'); }
    public function jamKeluarOven(): BelongsTo { return $this->belongsTo(JamKeluarOven::class, 'jam_keluar_oven_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class, 'customer_id'); }
    public function modelSize(): BelongsTo { return $this->belongsTo(ModelSize::class, 'modelsize_id'); }
    public function spesifikasi(): BelongsTo { return $this->belongsTo(Spesifikasi::class, 'spesifikasi_id'); }
}
