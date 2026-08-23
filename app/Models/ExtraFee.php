<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtraFee extends Model
{
    protected $fillable = [
        'name',
        'code',
        'amount',
        'applies_dsh',
        'period',
        'currency_id',
        'tax_rate_id',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'applies_dsh' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function calculationExtraFees(): HasMany
    {
        return $this->hasMany(CalculationExtraFee::class);
    }
}
