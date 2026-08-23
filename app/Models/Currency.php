<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'active',
    ];

    protected function casts(): array
    {
        return [
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
