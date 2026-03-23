<?php

namespace App\Filament\Resources\DemoCategoryResource\Pages;

use App\Filament\Resources\DemoCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDemoCategories extends ListRecords
{
    protected static string $resource = DemoCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nueva categoria'),
        ];
    }
}
