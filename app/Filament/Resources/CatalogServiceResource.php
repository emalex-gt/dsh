<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatalogServiceResource\Pages;
use App\Filament\Resources\CatalogServiceResource\RelationManagers\ItemsRelationManager;
use App\Models\CatalogService;
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

class CatalogServiceResource extends Resource
{
    protected static ?string $model = CatalogService::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Catalogo servicios';

    protected static ?string $navigationLabel = 'Servicios';

    protected static ?string $modelLabel = 'servicio';

    protected static ?string $pluralModelLabel = 'servicios';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('service_subcategory_id')
                ->label('Subcategoria')
                ->options(ServiceSubcategory::query()->with('category')->orderBy('sort_order')->get()->mapWithKeys(
                    fn (ServiceSubcategory $subcategory) => [$subcategory->id => "{$subcategory->category->name} / {$subcategory->name}"]
                ))
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
                ->label('Activo')
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
                TextColumn::make('subcategory.category.name')
                    ->label('Categoria')
                    ->badge(),
                TextColumn::make('subcategory.name')
                    ->label('Subcategoria')
                    ->badge(),
                TextColumn::make('code')
                    ->label('Codigo')
                    ->badge(),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge(),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_subcategory_id')
                    ->label('Subcategoria')
                    ->relationship('subcategory', 'name'),
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
            'index' => Pages\ListCatalogServices::route('/'),
            'create' => Pages\CreateCatalogService::route('/create'),
            'edit' => Pages\EditCatalogService::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }
}
