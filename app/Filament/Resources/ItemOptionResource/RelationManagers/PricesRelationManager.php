<?php

namespace App\Filament\Resources\ItemOptionResource\RelationManagers;

use App\Models\Currency;
use App\Models\TaxRate;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $title = 'Precios de la opcion';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('price_type')
                ->label('Tipo de precio')
                ->options([
                    'FIRST_YEAR' => 'Primer año',
                    'RENEWAL' => 'Renovacion',
                    'ONE_TIME' => 'Pago unico',
                ])
                ->required(),
            Forms\Components\TextInput::make('price')->label('Precio')->numeric()->prefix('€')->required(),
            Forms\Components\Select::make('currency_id')
                ->label('Moneda')
                ->options(Currency::query()->orderBy('code')->pluck('code', 'id'))
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('tax_rate_id')
                ->label('Impuesto')
                ->options(TaxRate::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload(),
            Forms\Components\DatePicker::make('valid_from')->label('Valido desde')->native(false),
            Forms\Components\DatePicker::make('valid_to')->label('Valido hasta')->native(false),
            Forms\Components\Toggle::make('active')->label('Activo')->default(true),
            Forms\Components\Textarea::make('notes')->label('Notas')->rows(3)->columnSpanFull(),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Precios de la opcion')
            ->description('Registra los importes vigentes por periodo; el IVA se gestionara aparte de la estimacion.')
            ->columns([
                Tables\Columns\TextColumn::make('price_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'FIRST_YEAR' => 'Primer año',
                        'RENEWAL' => 'Renovacion',
                        'ONE_TIME' => 'Pago unico',
                        default => $state,
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('price')->label('Precio')->money('EUR'),
                Tables\Columns\TextColumn::make('valid_from')->label('Valido desde')->date('d/m/Y')->placeholder('Sin limite'),
                Tables\Columns\TextColumn::make('valid_to')->label('Valido hasta')->date('d/m/Y')->placeholder('Sin limite'),
                Tables\Columns\IconColumn::make('active')->label('Activo')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Anadir precio'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ]);
    }
}
