<?php

namespace App\Filament\Resources\ClientUserResource\Pages;

use App\Filament\Resources\ClientUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClientUser extends CreateRecord
{
    protected static string $resource = ClientUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_admin'] = false;
        $data['project_name'] = $data['project_name'] ?: 'Demo Proyecto';

        return $data;
    }
}
