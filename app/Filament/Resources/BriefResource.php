<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BriefResource\Pages;
use App\Models\Brief;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BriefResource extends Resource
{
    protected static ?string $model = Brief::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Clientes';

    protected static ?string $navigationLabel = 'Briefs';

    protected static ?string $modelLabel = 'brief';

    protected static ?string $pluralModelLabel = 'briefs';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->latest('submitted_at')->latest('id'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('company')
                    ->label('Empresa')
                    ->state(fn (Brief $record): string => self::companyName($record))
                    ->description(fn (Brief $record): string => self::value($record, 'legal_name'))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->where('data->brand_name', 'like', "%{$search}%")
                        ->orWhere('data->legal_name', 'like', "%{$search}%")),
                TextColumn::make('contact')
                    ->label('Contacto')
                    ->state(fn (Brief $record): string => self::value($record, 'contact_name'))
                    ->description(fn (Brief $record): string => self::value($record, 'contact_email'))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->where('data->contact_name', 'like', "%{$search}%")
                        ->orWhere('data->contact_email', 'like', "%{$search}%")),
                TextColumn::make('services')
                    ->label('Servicios')
                    ->badge()
                    ->separator(',')
                    ->state(fn (Brief $record): array => self::serviceLabels((array) data_get($record->data, 'selected_services', []))),
                TextColumn::make('investment_budget')
                    ->label('Presupuesto')
                    ->state(fn (Brief $record): string => self::budget($record)),
                TextColumn::make('origin')
                    ->label('Origen')
                    ->badge()
                    ->color(fn (Brief $record): string => $record->user_id ? 'success' : 'gray')
                    ->state(fn (Brief $record): string => $record->user_id ? 'Cliente' : 'Publico'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusLabel($state))
                    ->color(fn (string $state): string => self::statusColor($state)),
                TextColumn::make('submitted_at')
                    ->label('Enviado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(self::statusOptions()),
                Tables\Filters\SelectFilter::make('origen')
                    ->label('Origen')
                    ->options([
                        'cliente' => 'Cliente',
                        'publico' => 'Publico',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'cliente' => $query->whereNotNull('user_id'),
                            'publico' => $query->whereNull('user_id'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Abrir'),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Resumen operativo')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => self::statusLabel($state))
                            ->color(fn (string $state): string => self::statusColor($state)),
                        TextEntry::make('origin')
                            ->label('Origen')
                            ->badge()
                            ->color(fn (Brief $record): string => $record->user_id ? 'success' : 'gray')
                            ->state(fn (Brief $record): string => $record->user_id ? 'Cliente' : 'Publico'),
                        TextEntry::make('submitted_at')
                            ->label('Recibido')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('client_reference')
                            ->label('Cliente vinculado')
                            ->state(fn (Brief $record): string => $record->user?->name ?: 'Envio publico')
                            ->helperText(fn (Brief $record): string => $record->user?->email ?: 'Sin cuenta asociada'),
                        TextEntry::make('company_name')
                            ->label('Empresa')
                            ->state(fn (Brief $record): string => self::companyName($record)),
                        TextEntry::make('contact_summary')
                            ->label('Contacto')
                            ->state(fn (Brief $record): string => self::value($record, 'contact_name'))
                            ->helperText(fn (Brief $record): string => self::value($record, 'contact_role')),
                        TextEntry::make('investment_budget')
                            ->label('Presupuesto estimado')
                            ->state(fn (Brief $record): string => self::budget($record)),
                        TextEntry::make('selected_services')
                            ->label('Servicios pedidos')
                            ->badge()
                            ->separator(',')
                            ->state(fn (Brief $record): array => self::serviceLabels((array) data_get($record->data, 'selected_services', []))),
                    ])
                    ->columns(4),
                Section::make('Empresa y contacto')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('legal_name')
                            ->label('Razon social')
                            ->state(fn (Brief $record): string => self::value($record, 'legal_name')),
                        TextEntry::make('brand_name')
                            ->label('Marca')
                            ->state(fn (Brief $record): string => self::value($record, 'brand_name')),
                        TextEntry::make('country')
                            ->label('Pais')
                            ->state(fn (Brief $record): string => self::value($record, 'country')),
                        TextEntry::make('city')
                            ->label('Ciudad')
                            ->state(fn (Brief $record): string => self::value($record, 'city')),
                        TextEntry::make('website')
                            ->label('Sitio web')
                            ->state(fn (Brief $record): string => self::value($record, 'website'))
                            ->url(fn (Brief $record): ?string => self::urlValue($record, 'website'), shouldOpenInNewTab: true)
                            ->copyable(),
                        TextEntry::make('social_links')
                            ->label('Redes y enlaces')
                            ->state(fn (Brief $record): string => self::multiline($record, 'social_links'))
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('contact_name')
                            ->label('Nombre de contacto')
                            ->state(fn (Brief $record): string => self::value($record, 'contact_name')),
                        TextEntry::make('contact_role')
                            ->label('Cargo')
                            ->state(fn (Brief $record): string => self::value($record, 'contact_role')),
                        TextEntry::make('contact_email')
                            ->label('Email')
                            ->state(fn (Brief $record): string => self::value($record, 'contact_email'))
                            ->url(fn (Brief $record): ?string => self::emailValue($record, 'contact_email'))
                            ->copyable(),
                        TextEntry::make('contact_phone')
                            ->label('Telefono')
                            ->state(fn (Brief $record): string => self::value($record, 'contact_phone'))
                            ->copyable(),
                    ])
                    ->columns(4),
                Section::make('Objetivo comercial')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('project_objectives')
                            ->label('Objetivos')
                            ->badge()
                            ->separator(',')
                            ->state(fn (Brief $record): array => self::textList((array) data_get($record->data, 'project_objectives', []))),
                        TextEntry::make('project_objectives_other')
                            ->label('Otro objetivo')
                            ->state(fn (Brief $record): string => self::value($record, 'project_objectives_other')),
                        TextEntry::make('results_timeframe')
                            ->label('Horizonte esperado')
                            ->state(fn (Brief $record): string => self::value($record, 'results_timeframe')),
                        TextEntry::make('business_model')
                            ->label('Modelo de negocio')
                            ->state(fn (Brief $record): string => self::value($record, 'business_model')),
                        TextEntry::make('ideal_customer')
                            ->label('Cliente ideal')
                            ->state(fn (Brief $record): string => self::multiline($record, 'ideal_customer'))
                            ->html()
                            ->columnSpan(2),
                        TextEntry::make('main_market')
                            ->label('Mercado principal')
                            ->state(fn (Brief $record): string => self::value($record, 'main_market')),
                        TextEntry::make('average_age')
                            ->label('Edad media')
                            ->state(fn (Brief $record): string => self::value($record, 'average_age')),
                        TextEntry::make('average_ticket')
                            ->label('Ticket medio')
                            ->state(fn (Brief $record): string => self::value($record, 'average_ticket')),
                        TextEntry::make('current_customer_acquisition')
                            ->label('Captacion actual')
                            ->state(fn (Brief $record): string => self::multiline($record, 'current_customer_acquisition'))
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(4),
                Section::make('Servicios solicitados')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('selected_services_summary')
                            ->label('Servicios elegidos')
                            ->badge()
                            ->separator(',')
                            ->state(fn (Brief $record): array => self::serviceLabels((array) data_get($record->data, 'selected_services', [])))
                            ->columnSpanFull(),
                        TextEntry::make('web_block')
                            ->label('Web')
                            ->state(fn (Brief $record): string => self::serviceBlock($record, 'web_pack', 'web_requirements'))
                            ->html()
                            ->columnSpan(2),
                        TextEntry::make('apps_block')
                            ->label('Apps')
                            ->state(fn (Brief $record): string => self::serviceBlock($record, 'apps_pack', 'apps_requirements'))
                            ->html()
                            ->columnSpan(2),
                        TextEntry::make('store_block')
                            ->label('Tienda online')
                            ->state(fn (Brief $record): string => self::serviceBlock($record, 'store_pack', 'store_requirements'))
                            ->html()
                            ->columnSpan(2),
                        TextEntry::make('design_block')
                            ->label('UX / UI')
                            ->state(fn (Brief $record): string => self::serviceBlock($record, 'design_scope', 'design_requirements'))
                            ->html()
                            ->columnSpan(2),
                        TextEntry::make('systems_type')
                            ->label('Sistemas')
                            ->badge()
                            ->separator(',')
                            ->state(fn (Brief $record): array => self::textList((array) data_get($record->data, 'systems_type', [])))
                            ->columnSpanFull(),
                        TextEntry::make('systems_requirements')
                            ->label('Requisitos de sistemas')
                            ->state(fn (Brief $record): string => self::multiline($record, 'systems_requirements'))
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(4),
                Section::make('Marca, competencia y contexto')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('brand_tone')
                            ->label('Tono de marca')
                            ->badge()
                            ->separator(',')
                            ->state(fn (Brief $record): array => self::textList((array) data_get($record->data, 'brand_tone', []))),
                        TextEntry::make('brand_tone_other')
                            ->label('Otro tono')
                            ->state(fn (Brief $record): string => self::value($record, 'brand_tone_other')),
                        TextEntry::make('brand_values')
                            ->label('Valores')
                            ->state(fn (Brief $record): string => self::multiline($record, 'brand_values'))
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('competitive_differentiator')
                            ->label('Diferenciador')
                            ->state(fn (Brief $record): string => self::multiline($record, 'competitive_differentiator'))
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('main_competitors')
                            ->label('Competidores principales')
                            ->state(fn (Brief $record): string => self::multiline($record, 'main_competitors'))
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('competitors_best')
                            ->label('Lo mejor que ve en la competencia')
                            ->state(fn (Brief $record): string => self::multiline($record, 'competitors_best'))
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('competitors_worst')
                            ->label('Lo peor que ve en la competencia')
                            ->state(fn (Brief $record): string => self::multiline($record, 'competitors_worst'))
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('competitive_advantage')
                            ->label('Ventaja competitiva propia')
                            ->state(fn (Brief $record): string => self::multiline($record, 'competitive_advantage'))
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Operativa y timing')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('uses_crm')
                            ->label('Usa CRM')
                            ->badge()
                            ->color(fn (Brief $record): string => self::yesNoColor($record, 'uses_crm'))
                            ->state(fn (Brief $record): string => self::yesNo($record, 'uses_crm')),
                        TextEntry::make('uses_email_marketing')
                            ->label('Email marketing')
                            ->badge()
                            ->color(fn (Brief $record): string => self::yesNoColor($record, 'uses_email_marketing'))
                            ->state(fn (Brief $record): string => self::yesNo($record, 'uses_email_marketing')),
                        TextEntry::make('needs_funnels')
                            ->label('Funnels')
                            ->badge()
                            ->color(fn (Brief $record): string => self::yesNoColor($record, 'needs_funnels'))
                            ->state(fn (Brief $record): string => self::yesNo($record, 'needs_funnels')),
                        TextEntry::make('needs_sales_automation')
                            ->label('Automatizacion comercial')
                            ->badge()
                            ->color(fn (Brief $record): string => self::yesNoColor($record, 'needs_sales_automation'))
                            ->state(fn (Brief $record): string => self::yesNo($record, 'needs_sales_automation')),
                        TextEntry::make('needs_ads_integration')
                            ->label('Integracion Ads')
                            ->badge()
                            ->color(fn (Brief $record): string => self::yesNoColor($record, 'needs_ads_integration'))
                            ->state(fn (Brief $record): string => self::yesNo($record, 'needs_ads_integration')),
                        TextEntry::make('desired_start_date')
                            ->label('Inicio deseado')
                            ->state(fn (Brief $record): string => self::value($record, 'desired_start_date')),
                        TextEntry::make('has_deadline')
                            ->label('Tiene deadline')
                            ->badge()
                            ->color(fn (Brief $record): string => self::yesNoColor($record, 'has_deadline'))
                            ->state(fn (Brief $record): string => self::yesNo($record, 'has_deadline')),
                        TextEntry::make('deadline_date')
                            ->label('Fecha limite')
                            ->state(fn (Brief $record): string => self::value($record, 'deadline_date')),
                        TextEntry::make('has_launch_date')
                            ->label('Tiene lanzamiento')
                            ->badge()
                            ->color(fn (Brief $record): string => self::yesNoColor($record, 'has_launch_date'))
                            ->state(fn (Brief $record): string => self::yesNo($record, 'has_launch_date')),
                        TextEntry::make('launch_date')
                            ->label('Fecha de lanzamiento')
                            ->state(fn (Brief $record): string => self::value($record, 'launch_date')),
                    ])
                    ->columns(5),
                Section::make('Materiales y seguimiento')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('materials_available')
                            ->label('Material disponible')
                            ->badge()
                            ->separator(',')
                            ->state(fn (Brief $record): array => self::textList((array) data_get($record->data, 'materials_available', [])))
                            ->columnSpanFull(),
                        TextEntry::make('service_expectations')
                            ->label('Expectativas del servicio')
                            ->badge()
                            ->separator(',')
                            ->state(fn (Brief $record): array => self::textList((array) data_get($record->data, 'service_expectations', [])))
                            ->columnSpanFull(),
                        TextEntry::make('optional_support')
                            ->label('Soporte adicional')
                            ->badge()
                            ->separator(',')
                            ->state(fn (Brief $record): array => self::textList((array) data_get($record->data, 'optional_support', [])))
                            ->columnSpanFull(),
                        TextEntry::make('admin_notes')
                            ->label('Nota interna')
                            ->state(fn (Brief $record): string => self::plainText($record->admin_notes))
                            ->placeholder('Sin notas internas por ahora')
                            ->columnSpanFull(),
                    ]),
                Section::make('Fiscal')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('eu_registered')->label('Registrado en la UE')->state(fn (Brief $record): string => self::yesNo($record, 'eu_registered')),
                        TextEntry::make('vat_number')->label('IVA / VAT')->state(fn (Brief $record): string => self::value($record, 'vat_number')),
                        TextEntry::make('fiscal_name')->label('Nombre fiscal')->state(fn (Brief $record): string => self::value($record, 'fiscal_name')),
                        TextEntry::make('fiscal_address')->label('Direccion fiscal')->state(fn (Brief $record): string => self::value($record, 'fiscal_address')),
                        TextEntry::make('fiscal_postal_code')->label('Codigo postal')->state(fn (Brief $record): string => self::value($record, 'fiscal_postal_code')),
                        TextEntry::make('fiscal_country')->label('Pais fiscal')->state(fn (Brief $record): string => self::value($record, 'fiscal_country')),
                        TextEntry::make('commercial_registry_number')->label('Registro mercantil')->state(fn (Brief $record): string => self::value($record, 'commercial_registry_number')),
                        TextEntry::make('billing_type')->label('Facturacion')->state(fn (Brief $record): string => self::value($record, 'billing_type')),
                        TextEntry::make('intracommunity_invoice')->label('Factura intracomunitaria')->state(fn (Brief $record): string => self::yesNo($record, 'intracommunity_invoice')),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBriefs::route('/'),
            'view' => Pages\ViewBrief::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function statusOptions(): array
    {
        return [
            'submitted' => 'Pendiente',
            'reviewed' => 'Revisado',
            'contacted' => 'Contactado',
            'closed' => 'Cerrado',
        ];
    }

    public static function statusLabel(string $status): string
    {
        return self::statusOptions()[$status] ?? ucfirst($status);
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'submitted' => 'warning',
            'reviewed' => 'info',
            'contacted' => 'success',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    protected static function companyName(Brief $record): string
    {
        return self::value($record, 'brand_name', self::value($record, 'legal_name'));
    }

    protected static function serviceLabels(array $services): array
    {
        $map = [
            'web' => 'Web',
            'apps' => 'Apps',
            'store' => 'Tienda online',
            'design' => 'UX / UI',
            'systems' => 'Sistemas',
        ];

        return collect($services)
            ->map(fn (mixed $service): string => $map[$service] ?? (string) $service)
            ->values()
            ->all();
    }

    protected static function textList(array $values): array
    {
        return collect($values)
            ->filter(fn (mixed $value): bool => filled($value))
            ->map(fn (mixed $value): string => (string) $value)
            ->values()
            ->all();
    }

    protected static function value(Brief $record, string $key, string $fallback = '-'): string
    {
        $value = data_get($record->data, $key);

        if (blank($value)) {
            return $fallback;
        }

        return is_scalar($value) ? (string) $value : $fallback;
    }

    protected static function plainText(?string $value, string $fallback = '-'): string
    {
        if (blank($value)) {
            return $fallback;
        }

        return (string) $value;
    }

    protected static function multiline(Brief $record, string $key, string $fallback = '-'): string
    {
        $value = self::value($record, $key, $fallback);

        if ($value === $fallback) {
            return $fallback;
        }

        return nl2br(e($value));
    }

    protected static function urlValue(Brief $record, string $key): ?string
    {
        $value = self::value($record, $key, '');

        return filled($value) ? $value : null;
    }

    protected static function emailValue(Brief $record, string $key): ?string
    {
        $value = self::value($record, $key, '');

        return filled($value) ? "mailto:{$value}" : null;
    }

    protected static function serviceBlock(Brief $record, string $packKey, string $requirementsKey): string
    {
        $pack = self::value($record, $packKey, 'Sin pack especificado');
        $requirements = self::multiline($record, $requirementsKey, 'Sin requisitos indicados');

        return '<strong>Pack:</strong> '.e($pack).'<br><br><strong>Detalle:</strong><br>'.$requirements;
    }

    protected static function budget(Brief $record): string
    {
        $value = data_get($record->data, 'investment_budget');

        if (! is_numeric($value)) {
            return self::value($record, 'investment_budget');
        }

        return number_format((float) $value, 0, ',', '.').' EUR';
    }

    protected static function yesNo(Brief $record, string $key): string
    {
        return match (data_get($record->data, $key)) {
            'si' => 'Si',
            'no' => 'No',
            default => '-',
        };
    }

    protected static function yesNoColor(Brief $record, string $key): string
    {
        return match (data_get($record->data, $key)) {
            'si' => 'success',
            'no' => 'gray',
            default => 'gray',
        };
    }
}


