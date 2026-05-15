<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['customer'])]
#[Table('customer')]
class Customer extends Model
{
    public function thermalShockDetails(): HasMany
    {
        return $this->hasMany(ThermalShockDetail::class, 'customer_id');
    }
}
