<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentRequest extends Model
{
    protected $fillable = [
        'user_id',
        'brief_id',
        'subject',
        'priority',
        'status',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function brief(): BelongsTo
    {
        return $this->belongsTo(Brief::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DevelopmentRequestMessage::class);
    }

    public static function priorityOptions(): array
    {
        return [
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'abierto' => 'Abierto',
            'en_progreso' => 'En progreso',
            'finalizado' => 'Finalizado',
        ];
    }
}
