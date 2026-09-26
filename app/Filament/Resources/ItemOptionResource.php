<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemOptionResource\Pages;
use App\Filament\Resources\ItemOptionResource\RelationManagers\PricesRelationManager;
use App\Models\ItemOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ItemOptionResource extends Resource
{
    protected static ?string $model = ItemOption::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'opcion';

    protected static ?string $pluralModelLabel = 'opciones';

    public static function form(Form $form): Form
    {
        return $form->schema(static::formSchema())->columns(2);
    }

    public static function formSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->label('Nombre')->required()->maxLength(255),
            Forms\Components\TextInput::make('sort_order')->label('Orden')->numeric()->default(0)->required(),
            Forms\Components\Toggle::make('active')->label('Activa')->default(true),
            Forms\Components\Textarea::make('description')->label('Descripcion')->rows(3)->columnSpanFull(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Opcion'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItemOptions::route('/'),
            'edit' => Pages\EditItemOption::route('/{record}/edit'),
        ];
    }
}
