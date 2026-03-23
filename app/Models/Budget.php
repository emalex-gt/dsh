<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'pdf_path',
        'pdf_hash',
        'status',
        'issued_at',
        'accepted_at',
        'accepted_name',
        'accepted_ip',
        'accepted_user_agent',
        'client_notes',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'accepted_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function statusOptions(): array
    {
        return [
            'pendiente' => 'Pendiente',
            'aprobado' => 'Aprobado',
            'cambios_solicitados' => 'Cambios solicitados',
        ];
    }
}
