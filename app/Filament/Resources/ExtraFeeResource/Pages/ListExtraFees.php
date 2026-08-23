<?php

namespace App\Filament\Resources\ExtraFeeResource\Pages;

use App\Filament\Resources\ExtraFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExtraFees extends ListRecords
{
    protected static string $resource = ExtraFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
