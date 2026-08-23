<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionPrice extends Model
{
    protected $fillable = [
        'item_option_id',
        'price_type',
        'price',
        'currency_id',
        'tax_rate_id',
        'notes',
        'valid_from',
        'valid_to',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'valid_from' => 'date',
            'valid_to' => 'date',
            'active' => 'boolean',
        ];
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ItemOption::class, 'item_option_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }
}
