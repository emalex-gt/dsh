<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DemoResource\Pages;
use App\Models\Demo;
use App\Models\DemoCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DemoResource extends Resource
{
    protected static ?string $model = Demo::class;

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static ?string $navigationGroup = 'Proyecto';

    protected static ?string $navigationLabel = 'Demos';

    protected static ?string $modelLabel = 'demo';

    protected static ?string $pluralModelLabel = 'demos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('demo_category_id')
                ->label('Categoria')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nombre de la categoria')
                        ->required()
                        ->maxLength(255)
                        ->unique(table: DemoCategory::class, column: 'name'),
                ])
                ->createOptionUsing(fn (array $data): int => DemoCategory::create($data)->getKey())
                ->required(),
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            TextInput::make('link')
                ->label('Enlace')
                ->url()
                ->required()
                ->maxLength(2048),
            FileUpload::make('preview_image_desktop')
                ->label('Imagen a mockup escritorio')
                ->image()
                ->imageEditor()
                ->directory('demos/previews/desktop')
                ->disk('public')
                ->visibility('public')
                ->helperText('Opcional. Se mostrara dentro del mockup de escritorio.')
                ->columnSpanFull(),
            FileUpload::make('preview_image_mobile')
                ->label('Imagen a mockup movil')
                ->image()
                ->imageEditor()
                ->directory('demos/previews/mobile')
                ->disk('public')
                ->visibility('public')
                ->helperText('Opcional. Se mostrara dentro del mockup movil.')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('preview_image_desktop')
                    ->label('Desktop')
                    ->disk('public')
                    ->square(),
                ImageColumn::make('preview_image_mobile')
                    ->label('Mobile')
                    ->disk('public')
                    ->square(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge()
                    ->sortable(),
                TextColumn::make('link')
                    ->label('Enlace')
                    ->url(fn (Demo $record): string => $record->link, true)
                    ->limit(50)
                    ->copyable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('demo_category_id')
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
            'index' => Pages\ListDemos::route('/'),
            'create' => Pages\CreateDemo::route('/crear'),
            'edit' => Pages\EditDemo::route('/{record}/editar'),
        ];
    }
}
