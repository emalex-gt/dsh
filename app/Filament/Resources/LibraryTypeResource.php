<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LibraryTypeResource\Pages;
use App\Models\LibraryType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LibraryTypeResource extends Resource
{
    protected static ?string $model = LibraryType::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Catalogo servicios';

    protected static ?string $navigationLabel = 'Bibliotecas';

    protected static ?string $modelLabel = 'biblioteca';

    protected static ?string $pluralModelLabel = 'bibliotecas';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('Nombre')->required(),
            TextInput::make('code')->label('Codigo')->required()->unique(ignoreRecord: true),
            Select::make('adjustment_type')
                ->label('Tipo de ajuste')
                ->options([
                    'DISCOUNT' => 'Descuento',
                    'NONE' => 'Sin ajuste',
                    'INCREASE' => 'Incremento',
                ])
                ->required(),
            TextInput::make('adjustment_value')
                ->label('Valor de ajuste')
                ->numeric()
                ->required(),
            Toggle::make('active')->label('Activa')->default(true),
            Textarea::make('description')->label('Descripcion')->rows(3)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('code')->label('Codigo')->badge(),
                TextColumn::make('adjustment_type')->label('Ajuste')->badge(),
                TextColumn::make('adjustment_value')->label('Valor')->suffix('%'),
                IconColumn::make('active')->label('Activa')->boolean(),
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
            'index' => Pages\ListLibraryTypes::route('/'),
            'create' => Pages\CreateLibraryType::route('/create'),
            'edit' => Pages\EditLibraryType::route('/{record}/edit'),
        ];
    }
}
