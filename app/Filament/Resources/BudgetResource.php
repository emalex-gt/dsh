<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Budget;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Facturacion';

    protected static ?string $navigationLabel = 'Presupuestos';

    protected static ?string $modelLabel = 'presupuesto';

    protected static ?string $pluralModelLabel = 'presupuestos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('Cliente')
                ->relationship('user', 'name')
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
                ->default(now()),
            FileUpload::make('pdf_path')
                ->label('PDF del presupuesto')
                ->acceptedFileTypes(['application/pdf'])
                ->directory('budgets')
                ->disk('local')
                ->visibility('private')
                ->downloadable()
                ->openable()
                ->required(),
            Textarea::make('client_notes')
                ->label('Observaciones del cliente')
                ->rows(5)
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),
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
                    ->formatStateUsing(fn (string $state): string => Budget::statusOptions()[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'aprobado' => 'success',
                        'cambios_solicitados' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('accepted_name')
                    ->label('Respondido por')
                    ->placeholder('-'),
                TextColumn::make('client_notes')
                    ->label('Observaciones')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('issued_at')
                    ->label('Emitido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('responded_at')
                    ->label('Respondido')
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
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/crear'),
            'edit' => Pages\EditBudget::route('/{record}/editar'),
        ];
    }

    public static function mutateBudgetData(array $data): array
    {
        if (filled($data['pdf_path'] ?? null) && Storage::disk('local')->exists($data['pdf_path'])) {
            $data['pdf_hash'] = hash_file('sha256', Storage::disk('local')->path($data['pdf_path']));
        }

        return $data;
    }
}
