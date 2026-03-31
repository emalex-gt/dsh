<?php

namespace App\Filament\Resources\DevelopmentRequestResource\RelationManagers;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Conversacion';

    public function form(Form $form): Form
    {
        return $form->schema([
            Checkbox::make('is_internal')
                ->label('Nota interna')
                ->default(false),
            Textarea::make('message')
                ->label('Mensaje')
                ->rows(6)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at')
            ->columns([
                TextColumn::make('sender_type')
                    ->label('Remitente')
                    ->formatStateUsing(fn (string $state): string => $state === 'admin' ? 'Administracion' : 'Cliente')
                    ->badge(),
                IconColumn::make('is_internal')
                    ->label('Interna')
                    ->boolean(),
                TextColumn::make('message')
                    ->label('Mensaje')
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Responder')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['sender_type'] = 'admin';
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->after(function ($record): void {
                        $request = $this->getOwnerRecord();

                        $request->update([
                            'status' => $record->is_internal ? $request->status : 'en_progreso',
                            'last_message_at' => now(),
                        ]);
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
