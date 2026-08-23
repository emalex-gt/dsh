<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxRate extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'country',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'active' => 'boolean',
        ];
    }

    public function optionPrices(): HasMany
    {
        return $this->hasMany(OptionPrice::class);
    }

    public function extraFees(): HasMany
    {
        return $this->hasMany(ExtraFee::class);
    }
}
