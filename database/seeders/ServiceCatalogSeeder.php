<?php

namespace Database\Seeders;

use App\Models\Calculation;
use App\Models\CatalogService;
use App\Models\Currency;
use App\Models\ExtraFee;
use App\Models\ItemOption;
use App\Models\LibraryType;
use App\Models\OptionPrice;
use App\Models\ServiceCategory;
use App\Models\ServiceItem;
use App\Models\ServiceSubcategory;
use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class ServiceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $eur = Currency::query()->updateOrCreate(
            ['code' => 'EUR'],
            ['name' => 'Euro', 'symbol' => '€', 'active' => true],
        );

        $iva = TaxRate::query()->updateOrCreate(
            ['name' => 'IVA 21%'],
            ['rate' => 0.21, 'country' => 'ES', 'active' => true],
        );

        $libraryPro = LibraryType::query()->updateOrCreate(
            ['code' => 'PRO'],
            ['name' => 'Pro', 'adjustment_type' => 'DISCOUNT', 'adjustment_value' => -40, 'description' => 'Descuento del 40% sobre el total.', 'active' => true],
        );
        $libraryAmateur = LibraryType::query()->updateOrCreate(
            ['code' => 'AMATEUR'],
            ['name' => 'Amateur', 'adjustment_type' => 'NONE', 'adjustment_value' => 0, 'description' => 'Sin ajuste sobre el total.', 'active' => true],
        );
        $libraryNone = LibraryType::query()->updateOrCreate(
            ['code' => 'NONE'],
            ['name' => 'Nada', 'adjustment_type' => 'INCREASE', 'adjustment_value' => 50, 'description' => 'Incremento del 50% sobre el total.', 'active' => true],
        );

        $rgpdFee = ExtraFee::query()->firstOrNew(['code' => 'RGPD_ANUAL']);
        $rgpdFee->fill([
            'code' => 'RGPD_ANUAL',
            'name' => 'RGPD x 1 año',
            'amount' => 150,
            'applies_dsh' => true,
            'period' => '1 año',
            'currency_id' => $eur->id,
            'tax_rate_id' => $iva->id,
            'active' => true,
        ])->save();
        $maintenanceFee = ExtraFee::query()->firstOrNew(['code' => 'MANTENIMIENTO_ANUAL']);
        $maintenanceFee->fill([
            'code' => 'MANTENIMIENTO_ANUAL',
            'name' => 'Mantenimiento web x 1 año',
            'amount' => 125,
            'applies_dsh' => true,
            'period' => '1 año',
            'currency_id' => $eur->id,
            'tax_rate_id' => $iva->id,
            'active' => true,
        ])->save();

        $serviceMap = [
            'A_SERVICIOS' => [
                'name' => 'Servicios y demos',
                'code' => 'A',
                'subcategories' => [
                    'webs' => ['name' => 'Webs', 'code' => '1', 'services' => ['mini-web' => ['name' => 'Mini Web', 'code' => 'a'], 'web' => ['name' => 'Web', 'code' => 'b'], 'app' => ['name' => 'App', 'code' => 'c']]],
                    'tiendas' => ['name' => 'Tiendas', 'code' => '2', 'services' => ['mini-market' => ['name' => 'Mini Market', 'code' => 'a'], 'tienda' => ['name' => 'Tienda', 'code' => 'b'], 'app-ecommerce' => ['name' => 'App eCommerce', 'code' => 'c']]],
                    'sistemas' => ['name' => 'Sistemas', 'code' => '3', 'services' => ['pos' => ['name' => 'PoS', 'code' => 'a'], 'crm' => ['name' => 'CRM', 'code' => 'b'], 'erp' => ['name' => 'ERP', 'code' => 'c']]],
                ],
            ],
            'B_EQUIPO' => [
                'name' => 'Equipo tecnico',
                'code' => 'B',
                'subcategories' => [
                    'webs' => ['name' => 'Webs', 'code' => '1', 'services' => ['mini-web-tecnico' => ['name' => 'Mini Web', 'code' => 'a'], 'web-tecnico' => ['name' => 'Web', 'code' => 'b'], 'app-tecnico' => ['name' => 'App', 'code' => 'c']]],
                    'tiendas' => ['name' => 'Tiendas', 'code' => '2', 'services' => ['mini-market-tecnico' => ['name' => 'Mini Market', 'code' => 'a'], 'tienda-tecnico' => ['name' => 'Tienda', 'code' => 'b'], 'app-ecommerce-tecnico' => ['name' => 'App eCommerce', 'code' => 'c']]],
                    'sistemas' => ['name' => 'Sistemas', 'code' => '3', 'services' => ['pos-tecnico' => ['name' => 'PoS', 'code' => 'a'], 'crm-tecnico' => ['name' => 'CRM', 'code' => 'b'], 'erp-tecnico' => ['name' => 'ERP', 'code' => 'c']]],
                ],
            ],
        ];

        $services = [];

        foreach ($serviceMap as $type => $categoryData) {
            $category = ServiceCategory::query()->firstOrNew(['type' => $type]);
            $category->fill([
                'name' => $categoryData['name'],
                'code' => $categoryData['code'],
                'sort_order' => 0,
                'active' => true,
            ])->save();

            $sort = 0;
            foreach ($categoryData['subcategories'] as $subcategoryCode => $subcategoryData) {
                $subcategory = ServiceSubcategory::query()->firstOrNew([
                    'service_category_id' => $category->id,
                    'name' => $subcategoryData['name'],
                ]);
                $subcategory->fill([
                    'code' => $subcategoryData['code'],
                    'sort_order' => $sort++,
                    'active' => true,
                ])->save();

                $serviceSort = 0;
                foreach ($subcategoryData['services'] as $serviceKey => $serviceData) {
                    $services[$serviceKey] = CatalogService::query()->firstOrNew([
                        'service_subcategory_id' => $subcategory->id,
                        'name' => $serviceData['name'],
                    ]);
                    $services[$serviceKey]->fill([
                        'code' => $serviceData['code'],
                        'sort_order' => $serviceSort++,
                        'active' => true,
                    ])->save();
                }
            }
        }

        $this->seedCommercialMiniWeb($services['mini-web'], $eur, $iva);
        $this->seedTechnicalMiniWeb($services['mini-web-tecnico'], $eur, $iva, $libraryPro, $libraryAmateur, $libraryNone, $rgpdFee, $maintenanceFee);
        $this->seedTechnicalCalculations($services, $libraryPro, $libraryAmateur, $libraryNone, $rgpdFee, $maintenanceFee);
    }

    private function seedCommercialMiniWeb(CatalogService $service, Currency $eur, TaxRate $iva): void
    {
        $hosting = $this->upsertItem($service, 'Hosting y Dominio', 'LIST', 0, false, false);
        $development = $this->upsertItem($service, 'Desarrollo Codigo HTML y CSS', 'SERVICE', 1, true);
        $design = $this->upsertItem($service, 'Diseno Personalizado', 'SERVICE', 2, true);
        $library = $this->upsertItem($service, 'Biblioteca', 'OPTION', 3);
        $rgpd = $this->upsertItem($service, 'RGPD x 1 año', 'SERVICE', 4);
        $maintenance = $this->upsertItem($service, 'Mantenimiento web x 1 año', 'SERVICE', 5);

        $hostingPrices = [
            'Hosting S' => [['FIRST_YEAR', 19.90], ['RENEWAL', 67.19]],
            'Hosting M' => [['FIRST_YEAR', 39.90], ['RENEWAL', 100.80]],
            'Hosting L' => [['FIRST_YEAR', 39.90], ['RENEWAL', 134.40]],
            'Hosting XL' => [['FIRST_YEAR', 19.90], ['RENEWAL', 67.19]],
        ];

        foreach ($hostingPrices as $name => $prices) {
            $option = $this->upsertOption($hosting, $name);
            foreach ($prices as [$type, $price]) {
                $this->upsertPrice($option, $type, $price, $eur, $iva);
            }
        }

        $this->upsertOption($development, 'HTMLyCSS');
        $this->upsertOption($design, 'UI y UX');
        $this->upsertOption($library, 'Pro');
        $this->upsertOption($library, 'Amateur');
        $this->upsertOption($library, 'Nada');
        $this->upsertOption($rgpd, 'RGPD x 1 año');
        $this->upsertOption($maintenance, 'Mantenimiento web x 1 año');
    }

    private function seedTechnicalMiniWeb(
        CatalogService $service,
        Currency $eur,
        TaxRate $iva,
        LibraryType $libraryPro,
        LibraryType $libraryAmateur,
        LibraryType $libraryNone,
        ExtraFee $rgpdFee,
        ExtraFee $maintenanceFee
    ): void {
        $hosting = $this->upsertItem($service, 'Hosting y Dominio', 'LIST', 0, false, false);
        $development = $this->upsertItem($service, 'Desarrollo e Instalacion Codigo', 'SERVICE', 1, true);
        $design = $this->upsertItem($service, 'Diseno', 'SERVICE', 2, true);
        $library = $this->upsertItem($service, 'Biblioteca', 'OPTION', 3);
        $rgpd = $this->upsertItem($service, 'RGPD x 1 año', 'SERVICE', 4);
        $maintenance = $this->upsertItem($service, 'Mantenimiento web x 1 año', 'SERVICE', 5);

        $hostingPrices = [
            'Hosting S' => [['FIRST_YEAR', 19.90], ['RENEWAL', 67.19]],
            'Hosting M' => [['FIRST_YEAR', 39.90], ['RENEWAL', 100.80]],
            'Hosting L' => [['FIRST_YEAR', 39.90], ['RENEWAL', 134.40]],
            'Hosting XL' => [['FIRST_YEAR', 79.90], ['RENEWAL', 68.00]],
        ];

        foreach ($hostingPrices as $name => $prices) {
            $option = $this->upsertOption($hosting, $name);
            foreach ($prices as [$type, $price]) {
                $this->upsertPrice($option, $type, $price, $eur, $iva);
            }
        }

        $devOption = $this->upsertOption($development, 'HTMLyCSS');
        $this->upsertPrice($devOption, 'ONE_TIME', 375.00, $eur, $iva);

        $designOption = $this->upsertOption($design, 'UI/UX');
        $this->upsertPrice($designOption, 'ONE_TIME', 375.00, $eur, $iva);

        $proOption = $this->upsertOption($library, $libraryPro->name, 'Descuento del 40% sobre el total.');
        $amateurOption = $this->upsertOption($library, $libraryAmateur->name, 'Sin ajuste sobre el total.');
        $noneOption = $this->upsertOption($library, $libraryNone->name, 'Incremento del 50% sobre el total.');
        $this->upsertPrice($proOption, 'ONE_TIME', -40.00, $eur, null);
        $this->upsertPrice($amateurOption, 'ONE_TIME', 0.00, $eur, null);
        $this->upsertPrice($noneOption, 'ONE_TIME', 50.00, $eur, null);

        $rgpdOption = $this->upsertOption($rgpd, $rgpdFee->name);
        $this->upsertPrice($rgpdOption, 'ONE_TIME', 150.00, $eur, $iva);

        $maintenanceOption = $this->upsertOption($maintenance, $maintenanceFee->name);
        $this->upsertPrice($maintenanceOption, 'ONE_TIME', 125.00, $eur, $iva);
    }

    private function seedTechnicalCalculations(
        array $services,
        LibraryType $libraryPro,
        LibraryType $libraryAmateur,
        LibraryType $libraryNone,
        ExtraFee $rgpdFee,
        ExtraFee $maintenanceFee
    ): void {
        $definitions = [
            ['service' => $services['mini-web-tecnico'], 'scenario' => 'MINI_WEB', 'days' => 3, 'hours' => 5, 'people' => 2, 'rate' => 25, 'subtotal_hours' => 30, 'subtotal_amount' => 750, 'dsh' => 30, 'dsh_amount' => 225, 'base_total' => 1025, 'total' => 1332.50],
            ['service' => $services['web-tecnico'], 'scenario' => 'WEB', 'days' => 5, 'hours' => 5, 'people' => 2, 'rate' => 25, 'subtotal_hours' => 50, 'subtotal_amount' => 1250, 'dsh' => 30, 'dsh_amount' => 375, 'base_total' => 1250, 'total' => 1625.00],
            ['service' => $services['app-tecnico'], 'scenario' => 'APP', 'days' => 20, 'hours' => 5, 'people' => 2, 'rate' => 25, 'subtotal_hours' => 200, 'subtotal_amount' => 5000, 'dsh' => 30, 'dsh_amount' => 1500, 'base_total' => 5000, 'total' => 6500.00],
        ];

        foreach ($definitions as $definition) {
            foreach ([$libraryPro, $libraryAmateur, $libraryNone] as $libraryType) {
                $calculation = Calculation::query()->updateOrCreate(
                    [
                        'catalog_service_id' => $definition['service']->id,
                        'scenario' => $definition['scenario'],
                        'library_type_id' => $libraryType->id,
                    ],
                    [
                        'days' => $definition['days'],
                        'hours_per_day' => $definition['hours'],
                        'people' => $definition['people'],
                        'rate_per_hour' => $definition['rate'],
                        'subtotal_hours' => $definition['subtotal_hours'],
                        'subtotal_amount' => $definition['subtotal_amount'],
                        'dsh_percentage' => $definition['dsh'],
                        'dsh_amount' => $definition['dsh_amount'],
                        'base_total' => $definition['base_total'],
                        'total_final' => $this->adjustedTotal($definition['total'], $libraryType),
                        'notes' => 'Escenario base cargado desde el analisis inicial del cliente.',
                    ],
                );

                $calculation->extraFees()->updateOrCreate(
                    ['extra_fee_id' => $rgpdFee->id],
                    ['amount' => $rgpdFee->amount, 'notes' => 'RGPD anual'],
                );

                $calculation->extraFees()->updateOrCreate(
                    ['extra_fee_id' => $maintenanceFee->id],
                    ['amount' => $maintenanceFee->amount, 'notes' => 'Mantenimiento anual'],
                );
            }
        }
    }

    private function adjustedTotal(float $baseTotal, LibraryType $libraryType): float
    {
        return match ($libraryType->adjustment_type) {
            'DISCOUNT' => $baseTotal - ($baseTotal * (abs((float) $libraryType->adjustment_value) / 100)),
            'INCREASE' => $baseTotal + ($baseTotal * ((float) $libraryType->adjustment_value / 100)),
            default => $baseTotal,
        };
    }

    private function upsertItem(
        CatalogService $service,
        string $name,
        string $type,
        int $sortOrder,
        bool $isRequired = false,
        bool $appliesDsh = true,
    ): ServiceItem
    {
        return ServiceItem::query()->updateOrCreate(
            ['catalog_service_id' => $service->id, 'name' => $name],
            ['item_type' => $type, 'is_required' => $isRequired, 'applies_dsh' => $appliesDsh, 'sort_order' => $sortOrder, 'active' => true],
        );
    }

    private function upsertOption(ServiceItem $item, string $name, ?string $description = null): ItemOption
    {
        return ItemOption::query()->updateOrCreate(
            ['service_item_id' => $item->id, 'name' => $name],
            ['description' => $description, 'active' => true],
        );
    }

    private function upsertPrice(ItemOption $option, string $type, float $price, Currency $currency, ?TaxRate $taxRate): OptionPrice
    {
        return OptionPrice::query()->updateOrCreate(
            ['item_option_id' => $option->id, 'price_type' => $type],
            ['price' => $price, 'currency_id' => $currency->id, 'tax_rate_id' => $taxRate?->id, 'active' => true],
        );
    }
}
