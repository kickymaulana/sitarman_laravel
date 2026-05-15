<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['modelsize'])]
#[Table('modelsize')]
class ModelSize extends Model
{
    public function thermalShockDetails(): HasMany
    {
        return $this->hasMany(ThermalShockDetail::class, 'modelsize_id');
    }
}
