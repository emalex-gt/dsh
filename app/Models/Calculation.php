<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calculation extends Model
{
    protected $fillable = [
        'catalog_service_id',
        'scenario',
        'library_type_id',
        'days',
        'hours_per_day',
        'people',
        'rate_per_hour',
        'subtotal_hours',
        'subtotal_amount',
        'dsh_percentage',
        'dsh_amount',
        'base_total',
        'total_final',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'rate_per_hour' => 'decimal:2',
            'subtotal_amount' => 'decimal:2',
            'dsh_percentage' => 'decimal:2',
            'dsh_amount' => 'decimal:2',
            'base_total' => 'decimal:2',
            'total_final' => 'decimal:2',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(CatalogService::class, 'catalog_service_id');
    }

    public function libraryType(): BelongsTo
    {
        return $this->belongsTo(LibraryType::class);
    }

    public function extraFees(): HasMany
    {
        return $this->hasMany(CalculationExtraFee::class);
    }

    public function notesList(): HasMany
    {
        return $this->hasMany(CalculationNote::class);
    }
}
