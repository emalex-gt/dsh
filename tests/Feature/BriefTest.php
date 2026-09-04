<?php

namespace Tests\Feature;

use App\Models\Brief;
use App\Models\CatalogService;
use App\Models\ExtraFee;
use App\Models\ServiceCategory;
use Database\Seeders\ServiceCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BriefTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_catalog_uses_codes_separately_from_names(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $commercialCategory = ServiceCategory::query()->where('type', 'A_SERVICIOS')->firstOrFail();
        $this->assertSame('A', $commercialCategory->code);
        $this->assertSame('Servicios y demos', $commercialCategory->name);
        $this->assertSame('1', $commercialCategory->subcategories()->where('name', 'Webs')->value('code'));
        $this->assertSame('a', CatalogService::query()->where('name', 'Mini Web')->where('service_subcategory_id', $commercialCategory->subcategories()->where('name', 'Webs')->value('id'))->value('code'));
        $this->assertSame('RGPD_ANUAL', ExtraFee::query()->where('name', 'RGPD x 1 año')->value('code'));
    }

    public function test_public_brief_can_be_submitted_with_dynamic_service_configuration(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $service = CatalogService::query()
            ->where('name', 'Mini Web')
            ->whereHas('subcategory.category', fn ($query) => $query->where('type', 'A_SERVICIOS'))
            ->with('subcategory', 'items.options')
            ->firstOrFail();

        $hostingItem = $service->items->firstWhere('name', 'Hosting y Dominio');
        $libraryItem = $service->items->firstWhere('name', 'Biblioteca');
        $hostingOption = $hostingItem->options->firstWhere('name', 'Hosting M');
        $libraryOption = $libraryItem->options->firstWhere('name', 'Amateur');

        $response = $this->post(route('brief.update'), $this->payload($service, [
            (string) $hostingItem->id => $hostingOption->id,
            (string) $libraryItem->id => $libraryOption->id,
        ]));

        $response->assertRedirect();

        $brief = Brief::query()->latest('id')->first();

        $this->assertNotNull($brief);
        $this->assertSame('pendiente_confirmacion', $brief->status);
        $this->assertSame('Webs / Mini Web', data_get($brief->data, 'selected_service_summary'));
        $this->assertSame('Mini Web', data_get($brief->data, 'selected_service_name'));
        $this->assertSame($hostingOption->id, data_get($brief->data, "service_item_selections.{$hostingItem->id}"));
        $this->assertSame('Hosting M', data_get($brief->data, 'selected_service_items.0.option_name'));
        $this->assertTrue(collect((array) data_get($brief->data, 'selected_service_items', []))->contains(
            fn (array $item): bool => data_get($item, 'item_name') === 'Biblioteca' && data_get($item, 'option_name') === 'Amateur'
        ));
        $this->assertSame('calculated', data_get($brief->data, 'technical_estimate.status'));
        $this->assertSame('MINI_WEB', data_get($brief->data, 'technical_estimate.technical_service.scenario'));
        $this->assertSame('Amateur', data_get($brief->data, 'technical_estimate.library.name'));
        $this->assertSame(1332.5, data_get($brief->data, 'technical_estimate.budget.technical_base'));
        $this->assertSame(39.9, data_get($brief->data, 'technical_estimate.budget.selected_services_subtotal'));
        $this->assertSame(8.38, data_get($brief->data, 'technical_estimate.budget.selected_services_tax'));
        $this->assertSame(1372.4, data_get($brief->data, 'technical_estimate.budget.estimated_total'));
        $this->assertCount(0, data_get($brief->data, 'technical_estimate.selected_extra_fees'));
    }

    public function test_brief_accepts_current_website_without_a_protocol(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $service = CatalogService::query()
            ->where('name', 'Mini Web')
            ->whereHas('subcategory.category', fn ($query) => $query->where('type', 'A_SERVICIOS'))
            ->with('items.options')
            ->firstOrFail();
        $hostingItem = $service->items->firstWhere('name', 'Hosting y Dominio');
        $libraryItem = $service->items->firstWhere('name', 'Biblioteca');

        foreach (['www.miweb.com', 'miweb.xxx'] as $website) {
            $response = $this->post(route('brief.update'), array_merge(
                $this->payload($service, [
                    (string) $hostingItem->id => $hostingItem->options->first()->id,
                    (string) $libraryItem->id => $libraryItem->options->first()->id,
                ]),
                ['website' => $website],
            ));

            $response->assertRedirect(route('brief.thanks'));
            $this->assertSame($website, Brief::query()->latest('id')->firstOrFail()->data['website']);
        }
    }

    public function test_brief_preselects_services_from_url_parameters(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $services = CatalogService::query()
            ->whereHas('subcategory.category', fn ($query) => $query->where('type', 'A_SERVICIOS'))
            ->whereIn('name', ['Mini Web', 'Mini Market'])
            ->with('subcategory')
            ->get()
            ->keyBy('name');
        $miniWeb = $services->get('Mini Web');
        $miniMarket = $services->get('Mini Market');
        $parameters = collect([$miniWeb, $miniMarket])
            ->map(fn (CatalogService $service): string => "{$service->subcategory->code}.{$service->code}")
            ->implode(',');

        $response = $this->get(route('brief.edit', ['servicios' => $parameters]));

        $response->assertOk()
            ->assertViewHas('preselectedServiceIds', fn (array $ids): bool => $ids === [$miniWeb->id, $miniMarket->id]);
    }

    public function test_pending_brief_persists_confirmation_metadata(): void
    {
        $brief = Brief::create([
            'status' => 'pendiente_confirmacion',
            'data' => ['contact_email' => 'ana@example.test'],
            'confirmation_token_hash' => hash('sha256', 'token'),
            'confirmation_expires_at' => now()->addHour(),
        ]);

        $this->assertTrue($brief->isPendingConfirmation());
        $this->assertTrue($brief->isConfirmationAvailable());
    }

    public function test_brief_requires_required_service_items_for_selected_service(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $service = CatalogService::query()
            ->where('name', 'Mini Web')
            ->whereHas('subcategory.category', fn ($query) => $query->where('type', 'A_SERVICIOS'))
            ->with('subcategory', 'items.options')
            ->firstOrFail();

        $hostingItem = $service->items->firstWhere('name', 'Hosting y Dominio');
        $libraryItem = $service->items->firstWhere('name', 'Biblioteca');
        $hostingOption = $hostingItem->options->firstWhere('name', 'Hosting S');

        $response = $this->from(route('brief.edit'))
            ->post(route('brief.update'), $this->payload($service, [
                (string) $hostingItem->id => $hostingOption->id,
            ]));

        $response
            ->assertRedirect(route('brief.edit'))
            ->assertSessionHasErrors("service_item_selections.{$libraryItem->id}");
    }

    public function test_selected_recurring_fees_are_prepaid_in_the_first_estimate(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $service = CatalogService::query()
            ->where('name', 'Mini Web')
            ->whereHas('subcategory.category', fn ($query) => $query->where('type', 'A_SERVICIOS'))
            ->with('items.options')
            ->firstOrFail();
        $hosting = $service->items->firstWhere('name', 'Hosting y Dominio');
        $library = $service->items->firstWhere('name', 'Biblioteca');
        $rgpd = $service->items->firstWhere('name', 'RGPD x 1 año');
        $maintenance = $service->items->firstWhere('name', 'Mantenimiento web x 1 año');

        $this->post(route('brief.update'), $this->payload($service, [
            (string) $hosting->id => $hosting->options->firstWhere('name', 'Hosting M')->id,
            (string) $library->id => $library->options->firstWhere('name', 'Amateur')->id,
            (string) $rgpd->id => $rgpd->options->first()->id,
            (string) $maintenance->id => $maintenance->options->first()->id,
        ]));

        $estimate = Brief::query()->latest('id')->firstOrFail()->data['technical_estimate'];

        $this->assertSame(397.4, data_get($estimate, 'budget.selected_services_subtotal'));
        $this->assertSame(83.46, data_get($estimate, 'budget.selected_services_tax'));
        $this->assertSame(1729.9, data_get($estimate, 'budget.estimated_total'));
        $this->assertCount(2, data_get($estimate, 'selected_extra_fees'));
        $this->assertSame(195, data_get($estimate, 'selected_extra_fees.0.amount'));
        $this->assertSame(162.5, data_get($estimate, 'selected_extra_fees.1.amount'));
    }

    public function test_brief_rejects_multiple_services_from_the_same_subcategory(): void
    {
        $this->seed(ServiceCatalogSeeder::class);

        $services = CatalogService::query()
            ->whereHas('subcategory.category', fn ($query) => $query->where('type', 'A_SERVICIOS'))
            ->whereHas('subcategory', fn ($query) => $query->where('name', 'Webs'))
            ->with('items.options')
            ->take(2)
            ->get();

        $firstService = $services->first();
        $hostingItem = $firstService->items->firstWhere('name', 'Hosting y Dominio');
        $libraryItem = $firstService->items->firstWhere('name', 'Biblioteca');

        $response = $this->from(route('brief.edit'))->post(route('brief.update'), array_merge(
            $this->payload($firstService, [
                (string) $hostingItem->id => $hostingItem->options->first()->id,
                (string) $libraryItem->id => $libraryItem->options->first()->id,
            ]),
            ['selected_service_ids' => $services->pluck('id')->all()],
        ));

        $response->assertRedirect(route('brief.edit'))
            ->assertSessionHasErrors('selected_service_ids');
    }

    private function payload(CatalogService $service, array $serviceSelections): array
    {
        foreach ($service->items->where('is_required', true) as $item) {
            if ($item->options->count() === 1) {
                $serviceSelections[(string) $item->id] = $item->options->first()->id;
            }
        }

        return [
            'legal_name' => 'Empresa Demo SL',
            'brand_name' => 'Demo Premium',
            'country' => 'Espana',
            'city' => 'Madrid',
            'website' => 'https://demo.test',
            'social_links' => "https://instagram.com/demo\nhttps://linkedin.com/company/demo",
            'contact_name' => 'Ana Cliente',
            'contact_role' => 'Directora',
            'contact_email' => 'ana@demo.test',
            'contact_phone' => '+34 600 000 000',
            'selected_service_subcategory_id' => $service->service_subcategory_id,
            'selected_service_id' => $service->id,
            'service_item_selections' => $serviceSelections,
            'investment_budget' => 4500,
            'brand_tone' => ['Premium'],
            'brand_values' => 'Rapidez, claridad y foco comercial.',
            'competitive_differentiator' => 'Atencion directa y ejecucion rapida.',
            'main_competitors' => 'Competidor A y Competidor B.',
            'competitors_best' => 'Tienen buena presencia visual.',
            'competitors_worst' => 'Comunican lento y no convierten bien.',
            'competitive_advantage' => 'Ofrecemos seguimiento cercano y capacidad de implementacion.',
            'uses_crm' => 'si',
            'uses_email_marketing' => 'no',
            'needs_funnels' => 'si',
            'needs_sales_automation' => 'no',
            'needs_ads_integration' => 'si',
            'desired_start_date' => '2026-09-01',
            'has_deadline' => 'no',
            'has_launch_date' => 'no',
            'materials_available' => ['Logo'],
            'service_expectations' => ['Estrategia'],
            'optional_support' => ['Soporte tecnico'],
        ];
    }
}
