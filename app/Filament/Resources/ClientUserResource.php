<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientUserResource\Pages;
use App\Models\Demo;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

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
            TextInput::make('hosting_link')
                ->label('Hosting: enlace de acceso')
                ->url()
                ->maxLength(2048),
            TextInput::make('hosting_username')
                ->label('Hosting: usuario')
                ->maxLength(255),
            TextInput::make('hosting_password')
                ->label('Hosting: contrasena')
                ->maxLength(255),
            DatePicker::make('hosting_expires_at')
                ->label('Hosting: fecha de vencimiento')
                ->native(false),
            TextInput::make('hosting_price')
                ->label('Hosting: precio')
                ->maxLength(255)
                ->placeholder('Ej. 129 EUR / ano'),
            TextInput::make('domain_name')
                ->label('Dominio: nombre')
                ->maxLength(255)
                ->placeholder('Ej. www.dominio.com'),
            DatePicker::make('domain_expires_at')
                ->label('Dominio: fecha de vencimiento')
                ->native(false),
            TextInput::make('domain_price')
                ->label('Dominio: precio')
                ->maxLength(255)
                ->placeholder('Ej. 18 EUR / ano'),
            Repeater::make('emailAccounts')
                ->label('Correos electronicos')
                ->relationship()
                ->schema([
                    TextInput::make('label')
                        ->label('Etiqueta')
                        ->maxLength(255)
                        ->placeholder('Ej. Soporte'),
                    TextInput::make('email')
                        ->label('Correo electronico')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('access_link')
                        ->label('Enlace de acceso')
                        ->url()
                        ->maxLength(2048),
                    TextInput::make('username')
                        ->label('Usuario')
                        ->maxLength(255),
                    TextInput::make('password')
                        ->label('Contrasena')
                        ->maxLength(255),
                ])
                ->columnSpanFull()
                ->defaultItems(0)
                ->reorderable(false)
                ->addActionLabel('Agregar correo'),
            TextInput::make('direct_demo_name')
                ->label('Demo directa: nombre')
                ->maxLength(255)
                ->helperText('Opcional. Puedes usar esta demo personalizada sin asignar demos generales.'),
            TextInput::make('direct_demo_link')
                ->label('Demo directa: enlace')
                ->url()
                ->maxLength(2048),
            FileUpload::make('direct_demo_desktop_image')
                ->label('Demo directa: mockup escritorio')
                ->image()
                ->imageEditor()
                ->directory('clients/direct-demos/desktop')
                ->disk('public')
                ->visibility('public')
                ->columnSpanFull(),
            FileUpload::make('direct_demo_mobile_image')
                ->label('Demo directa: mockup movil')
                ->image()
                ->imageEditor()
                ->directory('clients/direct-demos/mobile')
                ->disk('public')
                ->visibility('public')
                ->columnSpanFull(),
            Select::make('demo_ids')
                ->label('Demos asignadas')
                ->multiple()
                ->preload()
                ->searchable()
                ->live()
                ->options(fn (): array => Demo::query()
                    ->with('category')
                    ->orderBy('name')
                    ->get()
                    ->mapWithKeys(fn (Demo $demo): array => [
                        $demo->id => ($demo->category?->name ? $demo->category->name.' / ' : '').$demo->name,
                    ])
                    ->all())
                ->dehydrated(false)
                ->helperText('Opcional. Si las asignas, tambien apareceran en Demo Proyecto para este cliente.')
                ->columnSpanFull(),
            Select::make('recommended_demo_id')
                ->label('Demo recomendada')
                ->preload()
                ->searchable()
                ->options(function (Get $get): array {
                    $demoIds = collect($get('demo_ids') ?? [])->filter()->map(fn ($id) => (int) $id)->all();

                    if ($demoIds === []) {
                        return [];
                    }

                    return Demo::query()
                        ->whereIn('id', $demoIds)
                        ->with('category')
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn (Demo $demo): array => [
                            $demo->id => ($demo->category?->name ? $demo->category->name.' / ' : '').$demo->name,
                        ])
                        ->all();
                })
                ->dehydrated(false)
                ->helperText('Opcional. Si la defines, esa demo asignada se mostrara destacada al inicio.')
                ->columnSpanFull(),
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
                ->with(['latestBrief', 'demos'])
                ->withCount(['briefs', 'demos'])
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
                TextColumn::make('demos_count')
                    ->label('Demos')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'info' : 'gray'),
                TextColumn::make('direct_demo_name')
                    ->label('Demo directa')
                    ->placeholder('Sin demo directa')
                    ->toggleable(),
                TextColumn::make('recommended_demo')
                    ->label('Recomendada')
                    ->state(function (User $record): string {
                        $recommended = $record->demos->first(fn (Demo $demo) => (bool) $demo->pivot?->is_recommended);

                        return $recommended?->name ?? 'Sin recomendada';
                    })
                    ->toggleable(),
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
            ->with(['latestBrief', 'demos'])
            ->withCount(['briefs', 'demos']);
    }

    public static function syncClientDemos(User $user, array $data): void
    {
        $demoIds = collect($data['demo_ids'] ?? [])->filter()->map(fn ($id) => (int) $id)->unique()->values();
        $recommendedDemoId = filled($data['recommended_demo_id'] ?? null) ? (int) $data['recommended_demo_id'] : null;

        if ($recommendedDemoId !== null && ! $demoIds->contains($recommendedDemoId)) {
            throw ValidationException::withMessages([
                'recommended_demo_id' => 'La demo recomendada debe estar asignada al cliente.',
            ]);
        }

        $syncData = $demoIds
            ->mapWithKeys(fn (int $demoId): array => [
                $demoId => ['is_recommended' => $recommendedDemoId === $demoId],
            ])
            ->all();

        $user->demos()->sync($syncData);
    }
}


