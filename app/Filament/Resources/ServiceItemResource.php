<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceItemResource\Pages;
use App\Filament\Resources\ServiceItemResource\RelationManagers\OptionsRelationManager;
use App\Models\ServiceItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceItemResource extends Resource
{
    protected static ?string $model = ServiceItem::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'item del servicio';

    protected static ?string $pluralModelLabel = 'items del servicio';

    public static function form(Form $form): Form
    {
        return $form->schema(static::formSchema())->columns(2);
    }

    public static function formSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->label('Nombre')->required()->maxLength(255),
            Forms\Components\Select::make('item_type')
                ->label('Tipo de item')
                ->options([
                    'LIST' => 'Lista de opciones',
                    'SERVICE' => 'Servicio individual',
                    'OPTION' => 'Opcion configurable',
                ])
                ->required(),
            Forms\Components\Toggle::make('is_required')
                ->label('Obligatorio')
                ->helperText('Si tiene una unica opcion, queda seleccionada y no se puede quitar.')
                ->default(false),
            Forms\Components\Toggle::make('applies_dsh')
                ->label('Aplicar DSH al precio')
                ->helperText('Desactivalo para hosting, dominio u otros importes sin DSH.')
                ->default(true),
            Forms\Components\TextInput::make('sort_order')->label('Orden')->numeric()->default(0)->required(),
            Forms\Components\Toggle::make('active')->label('Activo')->default(true),
            Forms\Components\Textarea::make('description')->label('Descripcion')->rows(3)->columnSpanFull(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Item'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceItems::route('/'),
            'edit' => Pages\EditServiceItem::route('/{record}/edit'),
        ];
    }
}
