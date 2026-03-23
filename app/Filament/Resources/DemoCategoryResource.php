<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DemoCategoryResource\Pages;
use App\Models\DemoCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DemoCategoryResource extends Resource
{
    protected static ?string $model = DemoCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Proyecto';

    protected static ?string $navigationLabel = 'Categorias demos';

    protected static ?string $modelLabel = 'categoria demo';

    protected static ?string $pluralModelLabel = 'categorias demos';

    protected static ?string $slug = 'categorias-demos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('demos_count')
                    ->label('Demos')
                    ->counts('demos')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
            'index' => Pages\ListDemoCategories::route('/'),
            'create' => Pages\CreateDemoCategory::route('/crear'),
            'edit' => Pages\EditDemoCategory::route('/{record}/editar'),
        ];
    }
}
