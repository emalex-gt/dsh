<?php

namespace App\Filament\Resources\ServiceSubcategoryResource\Pages;

use App\Filament\Resources\ServiceSubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceSubcategory extends EditRecord
{
    protected static string $resource = ServiceSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
