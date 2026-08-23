<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'adjustment_type',
        'adjustment_value',
        'description',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'adjustment_value' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function calculations(): HasMany
    {
        return $this->hasMany(Calculation::class);
    }
}
