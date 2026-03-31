<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DevelopmentRequestResource\Pages;
use App\Filament\Resources\DevelopmentRequestResource\RelationManagers\MessagesRelationManager;
use App\Models\DevelopmentRequest;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DevelopmentRequestResource extends Resource
{
    protected static ?string $model = DevelopmentRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Desarrollo';

    protected static ?string $navigationLabel = 'Edit Area';

    protected static ?string $modelLabel = 'solicitud de desarrollo';

    protected static ?string $pluralModelLabel = 'solicitudes de desarrollo';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('subject')
                ->label('Asunto')
                ->required()
                ->maxLength(255),
            Select::make('priority')
                ->label('Prioridad')
                ->options(DevelopmentRequest::priorityOptions())
                ->required(),
            Select::make('status')
                ->label('Estado')
                ->options(DevelopmentRequest::statusOptions())
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('last_message_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Asunto')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->formatStateUsing(fn (string $state): string => DevelopmentRequest::priorityOptions()[$state] ?? ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'alta' => 'danger',
                        'media' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => DevelopmentRequest::statusOptions()[$state] ?? ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'abierto' => 'warning',
                        'en_progreso' => 'info',
                        'finalizado' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('last_message_at')
                    ->label('Ultima actualizacion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(DevelopmentRequest::statusOptions()),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options(DevelopmentRequest::priorityOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Gestionar'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevelopmentRequests::route('/'),
            'edit' => Pages\EditDevelopmentRequest::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
