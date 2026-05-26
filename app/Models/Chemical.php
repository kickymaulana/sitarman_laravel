<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tgl_test',
    'kode_alkali',
    'alkali_jam_mulai',
    'alkali_jam_selesai',
    'kode_acid',
    'acid_jam_mulai',
    'acid_jam_selesai'
])]
#[Table('chemical')]
class Chemical extends Model
{
    public function produk_chemical(): HasMany
    {
        return $this->hasMany(ProdukChemical::class, 'chemical_id');
    }
}
