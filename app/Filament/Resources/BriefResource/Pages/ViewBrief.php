<?php

namespace App\Filament\Resources\BriefResource\Pages;

use App\Filament\Resources\BriefResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Hash;

class ViewBrief extends ViewRecord
{
    protected static string $resource = BriefResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('crearCliente')
                ->label('Crear cliente')
                ->icon('heroicon-o-user-plus')
                ->visible(fn (): bool => blank($this->record->user_id) && $this->record->status !== 'confirmado')
                ->fillForm([
                    'name' => data_get($this->record->data, 'contact_name') ?: data_get($this->record->data, 'brand_name') ?: data_get($this->record->data, 'legal_name'),
                    'email' => data_get($this->record->data, 'contact_email'),
                ])
                ->form([
                    TextInput::make('name')
                        ->label('Nombre del cliente')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label('Correo electronico')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(table: User::class, column: 'email'),
                    TextInput::make('password')
                        ->label('Contrasena temporal')
                        ->password()
                        ->revealable()
                        ->required()
                        ->minLength(8),
                ])
                ->action(function (array $data): void {
                    $briefData = (array) $this->record->data;

                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'email_verified_at' => now(),
                        'password' => Hash::make($data['password']),
                        'is_admin' => false,
                        'project_name' => data_get($briefData, 'brand_name') ?: data_get($briefData, 'legal_name') ?: 'Demo Proyecto',
                        'legal_name' => data_get($briefData, 'legal_name'),
                        'brand_name' => data_get($briefData, 'brand_name'),
                        'contact_name' => data_get($briefData, 'contact_name'),
                        'contact_role' => data_get($briefData, 'contact_role'),
                        'contact_phone' => data_get($briefData, 'contact_phone'),
                        'country' => data_get($briefData, 'country'),
                        'city' => data_get($briefData, 'city'),
                    ]);

                    $this->record->update([
                        'user_id' => $user->id,
                    ]);

                    Notification::make()
                        ->title('Cliente creado y brief asignado')
                        ->body("Acceso creado para {$user->email}")
                        ->success()
                        ->send();
                }),
            Actions\Action::make('gestionar')
                ->label('Gestionar brief')
                ->icon('heroicon-o-pencil-square')
                ->fillForm([
                    'status' => $this->record->status,
                    'admin_notes' => $this->record->admin_notes,
                ])
                ->form([
                    Select::make('status')
                        ->label('Estado')
                        ->options(BriefResource::statusOptions())
                        ->required(),
                    Textarea::make('admin_notes')
                        ->label('Nota interna')
                        ->rows(6),
                ])
                ->action(function (array $data): void {
                    $this->record->update($data);

                    Notification::make()
                        ->title('Brief actualizado')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('volver')
                ->label('Volver')
                ->url(BriefResource::getUrl()),
        ];
    }
}
