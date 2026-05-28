<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'chemical_id',
    'tgl_produksi',
    'customer_id',
    'modelsize_id',
    'oven_id',
    'tanggal_keluar_oven',
    'jam_keluar_oven_id',
    'sample',
    'ketebalan_mm',
    'ketebalan_dm',
    'berat_awal',
    'berat_akhir',
    'density',
    'berat_hilang',
    'volume',
    'metode_biasa',
    'luas_permukaan',
    'hasil_akhir',

])]
#[Table('produk_chemical')]
class ProdukChemical extends Model
{
    public function chemical(): BelongsTo { return $this->belongsTo(Chemical::class, 'chemical_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class, 'customer_id'); }
    public function modelSize(): BelongsTo { return $this->belongsTo(ModelSize::class, 'modelsize_id'); }
    public function oven(): BelongsTo { return $this->belongsTo(Oven::class, 'oven_id'); }
    public function jamKeluarOven(): BelongsTo { return $this->belongsTo(JamKeluarOven::class, 'jam_keluar_oven_id'); }
}
