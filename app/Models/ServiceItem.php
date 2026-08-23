<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceItem extends Model
{
    protected $fillable = [
        'catalog_service_id',
        'name',
        'description',
        'item_type',
        'is_required',
        'applies_dsh',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'is_required' => 'boolean',
            'applies_dsh' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(CatalogService::class, 'catalog_service_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ItemOption::class)->orderBy('sort_order');
    }
}
