<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalculationExtraFee extends Model
{
    protected $fillable = [
        'calculation_id',
        'extra_fee_id',
        'amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function calculation(): BelongsTo
    {
        return $this->belongsTo(Calculation::class);
    }

    public function extraFee(): BelongsTo
    {
        return $this->belongsTo(ExtraFee::class);
    }
}
