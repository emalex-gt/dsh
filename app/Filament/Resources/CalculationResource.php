<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalculationResource\Pages;
use App\Models\Calculation;
use App\Models\CatalogService;
use App\Models\ExtraFee;
use App\Models\LibraryType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CalculationResource extends Resource
{
    protected static ?string $model = Calculation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Catalogo servicios';

    protected static ?string $navigationLabel = 'Calculos';

    protected static ?string $modelLabel = 'calculo';

    protected static ?string $pluralModelLabel = 'calculos';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('catalog_service_id')
                ->label('Servicio')
                ->options(CatalogService::query()->with('subcategory')->get()->mapWithKeys(
                    fn (CatalogService $service) => [$service->id => "{$service->subcategory->name} / {$service->name}"]
                ))
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('scenario')
                ->label('Escenario')
                ->required()
                ->maxLength(64),
            Select::make('library_type_id')
                ->label('Biblioteca')
                ->options(LibraryType::query()->where('active', true)->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload(),
            TextInput::make('days')->label('Dias')->numeric()->required()->default(0),
            TextInput::make('hours_per_day')->label('Horas por dia')->numeric()->required()->default(0),
            TextInput::make('people')->label('Personas')->numeric()->required()->default(0),
            TextInput::make('rate_per_hour')->label('Tarifa por hora')->numeric()->prefix('€')->required()->default(0),
            TextInput::make('subtotal_hours')->label('Horas subtotal')->numeric()->required()->default(0),
            TextInput::make('subtotal_amount')->label('Importe subtotal')->numeric()->prefix('€')->required()->default(0),
            TextInput::make('dsh_percentage')->label('% DSH')->numeric()->required()->default(0),
            TextInput::make('dsh_amount')->label('Importe DSH')->numeric()->prefix('€')->required()->default(0),
            TextInput::make('base_total')->label('Base total')->numeric()->prefix('€')->required()->default(0),
            TextInput::make('total_final')->label('Total final')->numeric()->prefix('€')->required()->default(0),
            Textarea::make('notes')->label('Notas generales')->rows(3)->columnSpanFull(),
            Repeater::make('extraFees')
                ->label('Cargos extra aplicados')
                ->relationship()
                ->collapsible()
                ->collapsed()
                ->itemLabel(fn (array $state): string => ExtraFee::query()->find($state['extra_fee_id'] ?? null)?->name ?? 'Nuevo cargo extra')
                ->schema([
                    Select::make('extra_fee_id')
                        ->label('Cargo extra')
                        ->options(ExtraFee::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('amount')
                        ->label('Importe aplicado')
                        ->numeric()
                        ->prefix('€'),
                    Textarea::make('notes')
                        ->label('Notas')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(2),
            Repeater::make('notesList')
                ->label('Notas de detalle')
                ->relationship()
                ->collapsible()
                ->collapsed()
                ->itemLabel(fn (array $state): string => filled($state['note'] ?? null) ? str($state['note'])->limit(70)->toString() : 'Nueva nota')
                ->schema([
                    Textarea::make('note')
                        ->label('Nota')
                        ->required()
                        ->rows(2),
                ]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.subcategory.name')
                    ->label('Subcategoria')
                    ->badge(),
                TextColumn::make('service.name')
                    ->label('Servicio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('scenario')
                    ->label('Escenario')
                    ->badge(),
                TextColumn::make('libraryType.name')
                    ->label('Biblioteca')
                    ->badge(),
                TextColumn::make('total_final')
                    ->label('Total final')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('dsh_percentage')
                    ->label('% DSH')
                    ->suffix('%'),
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
            'index' => Pages\ListCalculations::route('/'),
            'create' => Pages\CreateCalculation::route('/create'),
            'edit' => Pages\EditCalculation::route('/{record}/edit'),
        ];
    }
}
