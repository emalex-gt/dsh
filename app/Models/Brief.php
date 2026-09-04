<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brief extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'confirmation_token_hash',
        'confirmation_expires_at',
        'confirmed_at',
        'confirmed_name',
        'confirmed_ip',
        'confirmed_user_agent',
        'data',
        'admin_notes',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'submitted_at' => 'datetime',
            'confirmation_expires_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function isPendingConfirmation(): bool
    {
        return $this->status === 'pendiente_confirmacion';
    }

    public function isConfirmationAvailable(): bool
    {
        return $this->isPendingConfirmation()
            && filled($this->confirmation_token_hash)
            && $this->confirmation_expires_at?->isFuture();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
