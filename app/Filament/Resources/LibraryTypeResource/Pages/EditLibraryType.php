<?php

namespace App\Filament\Resources\LibraryTypeResource\Pages;

use App\Filament\Resources\LibraryTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLibraryType extends EditRecord
{
    protected static string $resource = LibraryTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
