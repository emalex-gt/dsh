<?php

namespace App\Filament\Resources\ClientUserResource\Pages;

use App\Filament\Resources\ClientUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientUser extends EditRecord
{
    protected static string $resource = ClientUserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['demo_ids'] = $this->record->demos()->pluck('demos.id')->all();
        $data['recommended_demo_id'] = $this->record->demos()
            ->wherePivot('is_recommended', true)
            ->value('demos.id');

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Eliminar'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['is_admin'] = false;
        $data['project_name'] = $data['project_name'] ?: 'Demo Proyecto';

        return $data;
    }

    protected function afterSave(): void
    {
        ClientUserResource::syncClientDemos($this->record, $this->data);
    }
}
