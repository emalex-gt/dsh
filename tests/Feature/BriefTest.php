<?php

namespace Tests\Feature;

use App\Models\Brief;
use App\Models\Demo;
use App\Models\DemoCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BriefTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_brief(): void
    {
        $this->get('/brief')->assertOk();
    }

    public function test_authenticated_user_can_view_brief(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/brief')
            ->assertOk();
    }

    public function test_guest_can_submit_brief(): void
    {
        $response = $this->post('/brief', $this->validPayload());

        $response->assertRedirect(route('brief.thanks'));
        $this->assertDatabaseHas('briefs', [
            'user_id' => null,
            'status' => 'submitted',
        ]);
    }

    public function test_guest_can_view_brief_thanks_page(): void
    {
        $this->get(route('brief.thanks'))
            ->assertOk()
            ->assertSee('Hemos guardado tu brief correctamente.');
    }

    public function test_selected_service_requires_pack_and_requirements(): void
    {
        $user = User::factory()->create();
        $payload = $this->validPayload();
        unset($payload['web_pack'], $payload['web_requirements']);

        $response = $this->actingAs($user)->from('/brief')->post('/brief', $payload);

        $response->assertRedirect('/brief');
        $response->assertSessionHasErrors(['web_pack', 'web_requirements']);
    }

    public function test_client_can_view_assigned_brief_in_private_area(): void
    {
        $user = User::factory()->create();

        Brief::create([
            'user_id' => $user->id,
            'status' => 'submitted',
            'data' => [
                'brand_name' => 'Demo Privado',
                'contact_name' => 'Ana Cliente',
                'contact_email' => 'cliente@demo.test',
                'selected_services' => ['web'],
            ],
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('client.brief.show'))
            ->assertOk()
            ->assertSee('Informacion base de tu proyecto')
            ->assertSee('Demo Privado');
    }

    public function test_client_can_view_private_data_page(): void
    {
        $user = User::factory()->create([
            'project_name' => 'Proyecto Demo',
            'brand_name' => 'Marca Demo',
        ]);

        $this->actingAs($user)
            ->get(route('client.data.show'))
            ->assertOk()
            ->assertSee('Datos base de tu cuenta y proyecto')
            ->assertSee('Proyecto Demo')
            ->assertSee('Ver Mi Brief');
    }

    public function test_client_can_view_direct_demo_without_general_demos(): void
    {
        $user = User::factory()->create([
            'direct_demo_name' => 'Mockup exclusivo',
            'direct_demo_link' => 'https://demo.test/directa',
        ]);

        $this->actingAs($user)
            ->get(route('client.demos.index'))
            ->assertOk()
            ->assertSee('Demo directa')
            ->assertSee('Mockup exclusivo')
            ->assertSee('https://demo.test/directa');
    }

    public function test_client_can_view_only_assigned_demos_with_recommended_first(): void
    {
        $user = User::factory()->create();
        $category = DemoCategory::create([
            'name' => 'Landing pages',
        ]);

        $recommendedDemo = Demo::create([
            'demo_category_id' => $category->id,
            'name' => 'Demo principal',
            'link' => 'https://demo.test/landing',
        ]);

        $secondaryDemo = Demo::create([
            'demo_category_id' => $category->id,
            'name' => 'Demo secundaria',
            'link' => 'https://demo.test/secundaria',
        ]);

        $hiddenDemo = Demo::create([
            'demo_category_id' => $category->id,
            'name' => 'Demo oculta',
            'link' => 'https://demo.test/oculta',
        ]);

        $user->demos()->sync([
            $recommendedDemo->id => ['is_recommended' => true],
            $secondaryDemo->id => ['is_recommended' => false],
        ]);

        $this->actingAs($user)
            ->get(route('client.demos.index'))
            ->assertOk()
            ->assertSee('Recomendada')
            ->assertSee('Demo principal')
            ->assertSee('Demo secundaria')
            ->assertDontSee('Demo oculta');
    }

    private function validPayload(): array
    {
        return [
            'legal_name' => 'Empresa Demo SL',
            'brand_name' => 'Demo',
            'country' => 'Espana',
            'city' => 'Madrid',
            'website' => 'https://demo.test',
            'social_links' => 'https://instagram.com/demo',
            'contact_name' => 'Ana Cliente',
            'contact_role' => 'CEO',
            'contact_email' => 'cliente@demo.test',
            'contact_phone' => '+34123456789',
            'eu_registered' => 'si',
            'vat_number' => 'ESB12345678',
            'fiscal_name' => 'Empresa Demo SL',
            'fiscal_address' => 'Calle Principal 123',
            'fiscal_postal_code' => '28001',
            'fiscal_country' => 'Espana',
            'commercial_registry_number' => 'RM-123',
            'billing_type' => 'B2B',
            'intracommunity_invoice' => 'si',
            'project_objectives' => ['Generar ventas'],
            'results_timeframe' => '3-6 meses',
            'ideal_customer' => 'Empresas medianas del sector servicios',
            'average_age' => '30-45',
            'main_market' => 'Espana',
            'business_model' => 'B2B',
            'average_ticket' => '1500 EUR',
            'current_customer_acquisition' => 'Recomendaciones y anuncios',
            'selected_services' => ['web'],
            'web_pack' => 'FullWeb',
            'web_requirements' => 'Necesitamos dominio, copywriting y multidioma.',
            'investment_budget' => '4500',
            'brand_tone' => ['Premium'],
            'brand_values' => 'Claridad, confianza, velocidad',
            'competitive_differentiator' => 'Atencion personalizada',
            'main_competitors' => 'Competidor A y Competidor B',
            'competitors_best' => 'Tienen buena presencia digital',
            'competitors_worst' => 'La experiencia de usuario es deficiente',
            'competitive_advantage' => 'Mas cercania y mejor soporte',
            'uses_crm' => 'no',
            'uses_email_marketing' => 'si',
            'needs_funnels' => 'si',
            'needs_sales_automation' => 'no',
            'needs_ads_integration' => 'si',
            'desired_start_date' => '2026-03-15',
            'has_deadline' => 'no',
            'has_launch_date' => 'no',
            'materials_available' => ['Logo'],
            'service_expectations' => ['Estrategia', 'Ejecucion tecnica'],
        ];
    }
}
