<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers\MessagesRelationManager;
use App\Models\Ticket;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $navigationGroup = 'Soporte';

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $modelLabel = 'ticket';

    protected static ?string $pluralModelLabel = 'tickets';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('subject')
                ->label('Asunto')
                ->required()
                ->maxLength(255),
            Select::make('category')
                ->label('Categoria')
                ->options(Ticket::categoryOptions())
                ->required(),
            Select::make('priority')
                ->label('Prioridad')
                ->options(Ticket::priorityOptions())
                ->required(),
            Select::make('status')
                ->label('Estado')
                ->options(Ticket::statusOptions())
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
                TextColumn::make('category')
                    ->label('Categoria')
                    ->formatStateUsing(fn (string $state): string => Ticket::categoryOptions()[$state] ?? ucfirst($state))
                    ->badge(),
                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->formatStateUsing(fn (string $state): string => Ticket::priorityOptions()[$state] ?? ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'alta' => 'danger',
                        'media' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => Ticket::statusOptions()[$state] ?? ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'abierto' => 'warning',
                        'en_proceso' => 'info',
                        'respondido' => 'success',
                        'cerrado' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('last_message_at')
                    ->label('Ultimo mensaje')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Ticket::statusOptions()),
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options(Ticket::priorityOptions()),
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
            'index' => Pages\ListTickets::route('/'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
