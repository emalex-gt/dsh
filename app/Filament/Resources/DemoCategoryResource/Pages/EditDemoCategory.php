<?php

namespace App\Filament\Resources\DemoCategoryResource\Pages;

use App\Filament\Resources\DemoCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDemoCategory extends EditRecord
{
    protected static string $resource = DemoCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Eliminar'),
        ];
    }
}
