<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtraFeeResource\Pages;
use App\Models\Currency;
use App\Models\ExtraFee;
use App\Models\TaxRate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExtraFeeResource extends Resource
{
    protected static ?string $model = ExtraFee::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Catalogo servicios';

    protected static ?string $navigationLabel = 'Cargos extra';

    protected static ?string $modelLabel = 'cargo extra';

    protected static ?string $pluralModelLabel = 'cargos extra';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('Nombre')->required(),
            TextInput::make('code')->label('Codigo')->required()->maxLength(64)->unique(ignoreRecord: true),
            TextInput::make('amount')->label('Importe')->numeric()->prefix('€')->required(),
            TextInput::make('period')->label('Periodo')->maxLength(64),
            Toggle::make('applies_dsh')
                ->label('Aplicar DSH al precio')
                ->helperText('Desactivalo para importes que no deban llevar DSH.')
                ->default(true),
            Select::make('currency_id')
                ->label('Moneda')
                ->options(Currency::query()->orderBy('code')->pluck('code', 'id'))
                ->searchable()
                ->preload(),
            Select::make('tax_rate_id')
                ->label('Impuesto')
                ->options(TaxRate::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload(),
            Toggle::make('active')->label('Activo')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('code')->label('Codigo')->badge()->searchable(),
                TextColumn::make('amount')->label('Importe')->money('EUR'),
                TextColumn::make('period')->label('Periodo'),
                IconColumn::make('applies_dsh')->label('DSH')->boolean(),
                TextColumn::make('currency.code')->label('Moneda')->badge(),
                TextColumn::make('taxRate.name')->label('Impuesto')->badge(),
                IconColumn::make('active')->label('Activo')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Eliminar'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtraFees::route('/'),
            'create' => Pages\CreateExtraFee::route('/create'),
            'edit' => Pages\EditExtraFee::route('/{record}/edit'),
        ];
    }
}
