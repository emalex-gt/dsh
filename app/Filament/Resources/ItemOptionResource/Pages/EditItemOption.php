<?php

namespace App\Filament\Resources\ItemOptionResource\Pages;

use App\Filament\Resources\ItemOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditItemOption extends EditRecord
{
    protected static string $resource = ItemOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Eliminar opcion'),
        ];
    }
}
