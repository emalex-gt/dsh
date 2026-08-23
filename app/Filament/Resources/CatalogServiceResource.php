<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatalogServiceResource\Pages;
use App\Models\CatalogService;
use App\Models\Currency;
use App\Models\ServiceSubcategory;
use App\Models\TaxRate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
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
            Repeater::make('items')
                ->label('Items del servicio')
                ->relationship()
                ->collapsible()
                ->collapsed()
                ->itemLabel(fn (array $state): string => filled($state['name'] ?? null) ? $state['name'] : 'Nuevo item')
                ->cloneable()
                ->reorderableWithButtons()
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required(),
                    Select::make('item_type')
                        ->label('Tipo de item')
                        ->options([
                            'LIST' => 'Lista de opciones',
                            'SERVICE' => 'Servicio individual',
                            'OPTION' => 'Opcion configurable',
                        ])
                        ->required(),
                    Toggle::make('is_required')
                        ->label('Obligatorio')
                        ->helperText('Si tiene una unica opcion, queda seleccionada y no se puede quitar.')
                        ->default(false),
                    Toggle::make('applies_dsh')
                        ->label('Aplicar DSH al precio')
                        ->helperText('Desactivalo para hosting, dominio u otros importes sin DSH.')
                        ->default(true),
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
                        ->rows(2)
                        ->columnSpanFull(),
                    Repeater::make('options')
                        ->label('Opciones')
                        ->relationship()
                        ->collapsible()
                        ->collapsed()
                        ->itemLabel(fn (array $state): string => filled($state['name'] ?? null) ? $state['name'] : 'Nueva opcion')
                        ->cloneable()
                        ->reorderableWithButtons()
                        ->schema([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->required(),
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
                                ->rows(2)
                                ->columnSpanFull(),
                            Repeater::make('prices')
                                ->label('Precios')
                                ->relationship()
                                ->collapsible()
                                ->collapsed()
                                ->itemLabel(function (array $state): string {
                                    $type = match ($state['price_type'] ?? null) {
                                        'FIRST_YEAR' => 'Primer año',
                                        'RENEWAL' => 'Renovación',
                                        'ONE_TIME' => 'Pago único',
                                        default => 'Nuevo precio',
                                    };

                                    return filled($state['price'] ?? null) ? "{$type}: {$state['price']} EUR" : $type;
                                })
                                ->cloneable()
                                ->schema([
                                    Select::make('price_type')
                                        ->label('Tipo de precio')
                                        ->options([
                                            'FIRST_YEAR' => 'Primer año',
                                            'RENEWAL' => 'Renovación',
                                            'ONE_TIME' => 'Pago único',
                                        ])
                                        ->required(),
                                    TextInput::make('price')
                                        ->label('Precio')
                                        ->numeric()
                                        ->prefix('€')
                                        ->required(),
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
                                    DatePicker::make('valid_from')
                                        ->label('Valido desde')
                                        ->native(false),
                                    DatePicker::make('valid_to')
                                        ->label('Valido hasta')
                                        ->native(false),
                                    Toggle::make('active')
                                        ->label('Activo')
                                        ->default(true),
                                    Textarea::make('notes')
                                        ->label('Notas')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                ])->columns(3),
                        ])->columns(3),
                ])->columnSpanFull(),
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
}
