<?php

namespace App\Filament\Resources\ServiceItemResource\RelationManagers;

use App\Filament\Resources\ItemOptionResource;
use App\Models\ItemOption;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $title = 'Opciones del item';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema(ItemOptionResource::formSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Opciones del item')
            ->description('Define las alternativas que el cliente puede seleccionar para este item.')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Opcion')->searchable(),
                Tables\Columns\TextColumn::make('prices_count')->label('Precios')->counts('prices')->badge(),
                Tables\Columns\IconColumn::make('active')->label('Activa')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Anadir opcion'),
            ])
            ->actions([
                Tables\Actions\Action::make('managePrices')
                    ->label('Precios')
                    ->icon('heroicon-o-currency-euro')
                    ->url(fn (ItemOption $record): string => ItemOptionResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ]);
    }
}
