<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceSubcategoryResource\Pages;
use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
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

class ServiceSubcategoryResource extends Resource
{
    protected static ?string $model = ServiceSubcategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'Catalogo servicios';

    protected static ?string $navigationLabel = 'Subcategorias';

    protected static ?string $modelLabel = 'subcategoria';

    protected static ?string $pluralModelLabel = 'subcategorias';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('service_category_id')
                ->label('Categoria')
                ->options(ServiceCategory::query()->orderBy('sort_order')->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            TextInput::make('code')
                ->label('Codigo')
                ->required()
                ->maxLength(255),
            TextInput::make('sort_order')
                ->label('Orden')
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('active')
                ->label('Activa')
                ->default(true),
            Textarea::make('description')
                ->label('Descripcion')
                ->rows(3)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Codigo')
                    ->badge(),
                TextColumn::make('services_count')
                    ->label('Servicios')
                    ->counts('services')
                    ->badge(),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                IconColumn::make('active')
                    ->label('Activa')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name'),
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
            'index' => Pages\ListServiceSubcategories::route('/'),
            'create' => Pages\CreateServiceSubcategory::route('/create'),
            'edit' => Pages\EditServiceSubcategory::route('/{record}/edit'),
        ];
    }
}
