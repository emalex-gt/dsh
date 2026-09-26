<?php

namespace App\Filament\Resources\ClientUserResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmailAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'emailAccounts';

    protected static ?string $title = 'Cuentas de correo';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')->label('Etiqueta')->maxLength(255)->placeholder('Ej. Soporte'),
            Forms\Components\TextInput::make('email')->label('Correo electronico')->email()->required()->maxLength(255),
            Forms\Components\TextInput::make('access_link')->label('Enlace de acceso')->url()->maxLength(2048),
            Forms\Components\TextInput::make('username')->label('Usuario')->maxLength(255),
            Forms\Components\TextInput::make('password')
                ->label('Contrasena')
                ->password()
                ->revealable()
                ->maxLength(255)
                ->dehydrated(fn (?string $state): bool => filled($state)),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Cuentas de correo')
            ->description('Administra las cuentas y credenciales de correo asignadas a este cliente.')
            ->columns([
                Tables\Columns\TextColumn::make('label')->label('Etiqueta')->placeholder('Sin etiqueta'),
                Tables\Columns\TextColumn::make('email')->label('Correo electronico')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('username')->label('Usuario')->placeholder('No definido')->toggleable(),
                Tables\Columns\TextColumn::make('access_link')->label('Acceso')->url(fn ($state) => $state)->openUrlInNewTab()->placeholder('No definido'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Anadir correo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ]);
    }
}
