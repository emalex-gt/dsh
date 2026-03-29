<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['status'] ?? 'no_pagada') === 'pagada') {
            $data['paid_at'] = $data['paid_at'] ?? now();
        } else {
            $data['paid_at'] = null;
        }

        return $data;
    }
}
