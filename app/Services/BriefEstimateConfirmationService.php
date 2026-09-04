<?php

namespace App\Services;

use App\Models\Brief;
use Illuminate\Support\Str;

class BriefEstimateConfirmationService
{
    public function createPending(array $data): array
    {
        $token = Str::random(64);
        $brief = Brief::create([
            'status' => 'pendiente_confirmacion',
            'confirmation_token_hash' => hash('sha256', $token),
            'confirmation_expires_at' => now()->addHours(48),
            'data' => $data,
            'submitted_at' => now(),
        ]);

        return compact('brief', 'token');
    }

    public function findAvailable(string $token): Brief
    {
        return Brief::query()
            ->where('confirmation_token_hash', hash('sha256', $token))
            ->where('status', 'pendiente_confirmacion')
            ->where('confirmation_expires_at', '>', now())
            ->firstOrFail();
    }
}
