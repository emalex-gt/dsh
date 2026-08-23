<?php

namespace App\Filament\Resources\CatalogServiceResource\Pages;

use App\Filament\Resources\CatalogServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCatalogServices extends ListRecords
{
    protected static string $resource = CatalogServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
