<?php

namespace App\Services;

use App\Models\Brief;
use App\Models\Budget;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function rotate(Brief $brief, array $data): string
    {
        $token = Str::random(64);
        $brief->update([
            'data' => $data,
            'confirmation_token_hash' => hash('sha256', $token),
            'confirmation_expires_at' => now()->addHours(48),
            'submitted_at' => now(),
        ]);

        return $token;
    }

    public function confirm(string $token, string $name, string $ip, string $userAgent): User
    {
        return DB::transaction(function () use ($token, $name, $ip, $userAgent) {
            $brief = Brief::query()->where('confirmation_token_hash', hash('sha256', $token))->lockForUpdate()->firstOrFail();
            abort_unless($brief->isConfirmationAvailable(), 404);
            $data = (array) $brief->data;
            abort_if(User::query()->where('email', data_get($data, 'contact_email'))->exists(), 422, 'Ya existe una cuenta con este correo.');
            $user = User::create([
                'name' => data_get($data, 'contact_name') ?: $name, 'email' => data_get($data, 'contact_email'),
                'password' => Hash::make(Str::random(64)), 'is_admin' => false, 'email_verified_at' => null,
                'project_name' => data_get($data, 'brand_name') ?: data_get($data, 'legal_name') ?: 'Demo Proyecto',
                'legal_name' => data_get($data, 'legal_name'), 'brand_name' => data_get($data, 'brand_name'),
                'contact_name' => data_get($data, 'contact_name'), 'contact_role' => data_get($data, 'contact_role'),
                'contact_phone' => data_get($data, 'contact_phone'), 'country' => data_get($data, 'country'), 'city' => data_get($data, 'city'),
            ]);
            $brief->update(['user_id' => $user->id, 'status' => 'confirmado', 'confirmed_at' => now(), 'confirmed_name' => $name, 'confirmed_ip' => $ip, 'confirmed_user_agent' => $userAgent, 'confirmation_token_hash' => null]);
            $path = "budgets/brief-{$brief->id}.pdf";
            Storage::disk('local')->put($path, Pdf::loadView('pdf.brief-estimate-budget', compact('brief', 'user'))->output());
            Budget::create(['user_id' => $user->id, 'title' => 'Presupuesto '.$user->project_name, 'pdf_path' => $path, 'pdf_hash' => hash_file('sha256', Storage::disk('local')->path($path)), 'status' => 'aprobado', 'issued_at' => now(), 'accepted_at' => now(), 'responded_at' => now(), 'accepted_name' => $name, 'accepted_ip' => $ip, 'accepted_user_agent' => $userAgent]);
            return $user;
        });
    }
}
