<?php

namespace App\Filament\Resources\CatalogServiceResource\RelationManagers;

use App\Filament\Resources\ServiceItemResource;
use App\Models\ServiceItem;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items del servicio';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema(ServiceItemResource::formSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Items del servicio')
            ->description('Crea los bloques que el cliente podra configurar dentro de este servicio.')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Item')->searchable(),
                Tables\Columns\TextColumn::make('item_type')->label('Tipo')->badge(),
                Tables\Columns\IconColumn::make('is_required')->label('Obligatorio')->boolean(),
                Tables\Columns\IconColumn::make('applies_dsh')->label('Aplica DSH')->boolean(),
                Tables\Columns\TextColumn::make('options_count')->label('Opciones')->counts('options')->badge(),
                Tables\Columns\IconColumn::make('active')->label('Activo')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Anadir item'),
            ])
            ->actions([
                Tables\Actions\Action::make('manageOptions')
                    ->label('Opciones')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->url(fn (ServiceItem $record): string => ServiceItemResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ]);
    }
}
