<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Facturacion';

    protected static ?string $navigationLabel = 'Facturas';

    protected static ?string $modelLabel = 'factura';

    protected static ?string $pluralModelLabel = 'facturas';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('Cliente')
                ->relationship('user', 'name', fn (Builder $query) => $query->where('is_admin', false))
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('title')
                ->label('Titulo')
                ->required()
                ->maxLength(255),
            DateTimePicker::make('issued_at')
                ->label('Fecha de emision')
                ->seconds(false)
                ->default(now())
                ->required(),
            Select::make('status')
                ->label('Estado')
                ->options(Invoice::statusOptions())
                ->default('no_pagada')
                ->required()
                ->native(false)
                ->live(),
            DateTimePicker::make('paid_at')
                ->label('Fecha de pago')
                ->seconds(false)
                ->visible(fn ($get) => $get('status') === 'pagada'),
            FileUpload::make('pdf_path')
                ->label('PDF de la factura')
                ->acceptedFileTypes(['application/pdf'])
                ->directory('invoices')
                ->disk('local')
                ->visibility('private')
                ->downloadable()
                ->openable()
                ->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('issued_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Invoice::statusOptions()[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => $state === 'pagada' ? 'success' : 'warning'),
                TextColumn::make('issued_at')
                    ->label('Emitida')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->label('Pagada')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/crear'),
            'edit' => Pages\EditInvoice::route('/{record}/editar'),
        ];
    }
}



