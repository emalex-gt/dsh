<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientUserResource\Pages;
use App\Models\Brief;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Usuarios';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'cliente';

    protected static ?string $pluralModelLabel = 'clientes';

    protected static ?string $slug = 'clientes';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('is_admin')
                ->default(false),
            TextInput::make('name')
                ->label('Nombre del cliente')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('Correo electronico')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            TextInput::make('password')
                ->label('Contrasena')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->minLength(8)
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->same('passwordConfirmation'),
            TextInput::make('passwordConfirmation')
                ->label('Confirmar contrasena')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(false),
            TextInput::make('project_name')
                ->label('Proyecto')
                ->maxLength(255)
                ->placeholder('Demo Proyecto'),
            TextInput::make('brand_name')
                ->label('Marca')
                ->maxLength(255),
            TextInput::make('legal_name')
                ->label('Razon social')
                ->maxLength(255),
            TextInput::make('contact_name')
                ->label('Persona de contacto')
                ->maxLength(255),
            TextInput::make('contact_role')
                ->label('Cargo')
                ->maxLength(255),
            TextInput::make('contact_phone')
                ->label('Telefono')
                ->tel()
                ->maxLength(255),
            TextInput::make('country')
                ->label('Pais')
                ->maxLength(120),
            TextInput::make('city')
                ->label('Ciudad')
                ->maxLength(120),
            Placeholder::make('brief_reference')
                ->label('Brief asignado')
                ->content(function (?User $record): string {
                    if (! $record) {
                        return 'Este cliente se esta creando manualmente y no tendra brief asignado por defecto.';
                    }

                    $latestBrief = $record->latestBrief;

                    if (! $latestBrief) {
                        return 'Este cliente no tiene brief asignado.';
                    }

                    $label = BriefResource::statusLabel($latestBrief->status);

                    return "Brief #{$latestBrief->id} / {$label} / Total briefs: {$record->briefs()->count()}";
                })
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->where('is_admin', false)
                ->with(['latestBrief'])
                ->withCount('briefs')
                ->latest('id'))
            ->columns([
                TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Correo electronico')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('project_name')
                    ->label('Proyecto')
                    ->placeholder('Demo Proyecto')
                    ->searchable(),
                TextColumn::make('brand_name')
                    ->label('Marca')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('briefs_count')
                    ->label('Briefs')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),
                TextColumn::make('latestBrief.status')
                    ->label('Estado brief')
                    ->badge()
                    ->state(fn (User $record): string => $record->latestBrief?->status ? BriefResource::statusLabel($record->latestBrief->status) : 'Sin brief')
                    ->color(fn (User $record): string => $record->latestBrief?->status ? BriefResource::statusColor($record->latestBrief->status) : 'gray'),
                TextColumn::make('contact_name')
                    ->label('Contacto')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brief')
                    ->label('Brief')
                    ->options([
                        'con_brief' => 'Con brief',
                        'sin_brief' => 'Sin brief',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'con_brief' => $query->has('briefs'),
                            'sin_brief' => $query->doesntHave('briefs'),
                            default => $query,
                        };
                    }),
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
            'index' => Pages\ListClientUsers::route('/'),
            'create' => Pages\CreateClientUser::route('/crear'),
            'edit' => Pages\EditClientUser::route('/{record}/editar'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_admin', false)
            ->with(['latestBrief'])
            ->withCount('briefs');
    }
}
