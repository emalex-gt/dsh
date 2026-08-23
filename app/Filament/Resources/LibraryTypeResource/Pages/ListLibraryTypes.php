<?php

namespace App\Filament\Resources\LibraryTypeResource\Pages;

use App\Filament\Resources\LibraryTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibraryTypes extends ListRecords
{
    protected static string $resource = LibraryTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
