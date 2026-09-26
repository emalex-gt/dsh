<?php

namespace Tests\Feature;

use App\Filament\Resources\CatalogServiceResource;
use App\Filament\Resources\CatalogServiceResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\ClientUserResource;
use App\Filament\Resources\ClientUserResource\RelationManagers\EmailAccountsRelationManager;
use App\Filament\Resources\ItemOptionResource;
use App\Filament\Resources\ItemOptionResource\RelationManagers\PricesRelationManager;
use App\Filament\Resources\ServiceItemResource;
use App\Filament\Resources\ServiceItemResource\RelationManagers\OptionsRelationManager;
use App\Models\Brief;
use App\Models\Budget;
use App\Models\CatalogService;
use App\Models\Demo;
use App\Models\DemoCategory;
use App\Models\DevelopmentRequest;
use App\Models\Invoice;
use App\Models\ItemOption;
use App\Models\ServiceCategory;
use App\Models\ServiceItem;
use App\Models\ServiceSubcategory;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_catalog_and_client_forms_expose_progressive_relation_managers(): void
    {
        $this->assertContains(ItemsRelationManager::class, CatalogServiceResource::getRelations());
        $this->assertContains(OptionsRelationManager::class, ServiceItemResource::getRelations());
        $this->assertContains(PricesRelationManager::class, ItemOptionResource::getRelations());
        $this->assertContains(EmailAccountsRelationManager::class, ClientUserResource::getRelations());
        $this->assertSame('item del servicio', ServiceItemResource::getModelLabel());
        $this->assertSame('opcion', ItemOptionResource::getModelLabel());
    }

    public function test_admin_can_open_nested_catalog_management_screens(): void
    {
        $admin = User::factory()->admin()->create();
        $category = ServiceCategory::create([
            'name' => 'Servicios',
            'code' => 'A',
            'type' => 'A_SERVICIOS',
        ]);
        $subcategory = ServiceSubcategory::create([
            'service_category_id' => $category->id,
            'name' => 'Webs',
            'code' => '1',
        ]);
        $service = CatalogService::create([
            'service_subcategory_id' => $subcategory->id,
            'name' => 'Mini Web',
            'code' => 'a',
        ]);
        $item = ServiceItem::create([
            'catalog_service_id' => $service->id,
            'name' => 'Hosting',
            'item_type' => 'OPTION',
        ]);
        $option = ItemOption::create([
            'service_item_id' => $item->id,
            'name' => 'Hosting M',
        ]);

        $this->actingAs($admin)
            ->get(ServiceItemResource::getUrl('edit', ['record' => $item]))
            ->assertOk();

        $this->actingAs($admin)
            ->get(ItemOptionResource::getUrl('edit', ['record' => $option]))
            ->assertOk();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_access_briefs_resource(): void
    {
        $admin = User::factory()->admin()->create();

        $brief = Brief::create([
            'status' => 'submitted',
            'data' => [
                'legal_name' => 'Empresa Demo SL',
                'brand_name' => 'Demo',
                'contact_name' => 'Ana Cliente',
                'contact_email' => 'cliente@demo.test',
                'selected_services' => ['web'],
            ],
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/briefs')
            ->assertOk()
            ->assertSee('Briefs')
            ->assertSee('Demo');

        $this->actingAs($admin)
            ->get("/admin/briefs/{$brief->id}")
            ->assertOk()
            ->assertSee('Resumen operativo')
            ->assertSee('Gestionar brief')
            ->assertSee('Crear cliente');
    }

    public function test_admin_can_access_tickets_resource(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->create();

        Ticket::create([
            'user_id' => $client->id,
            'subject' => 'Incidencia de acceso',
            'category' => 'acceso',
            'priority' => 'alta',
            'status' => 'abierto',
            'last_message_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/tickets')
            ->assertOk()
            ->assertSee('Tickets')
            ->assertSee('Incidencia de acceso');
    }

    public function test_admin_can_access_administrators_resource(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Principal',
            'email' => 'admin@demo.test',
        ]);

        User::factory()->admin()->create([
            'name' => 'Soporte Interno',
            'email' => 'soporte@demo.test',
        ]);

        $this->actingAs($admin)
            ->get('/admin/administradores')
            ->assertOk()
            ->assertSee('Administradores')
            ->assertSee('Soporte Interno');
    }

    public function test_admin_can_access_clients_resource(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->create([
            'name' => 'Cliente Manual',
            'email' => 'cliente.manual@demo.test',
            'project_name' => 'Proyecto Manual',
            'is_admin' => false,
        ]);

        Brief::create([
            'user_id' => $client->id,
            'status' => 'reviewed',
            'data' => [
                'brand_name' => 'Proyecto Manual',
                'contact_name' => 'Cliente Manual',
                'contact_email' => 'cliente.manual@demo.test',
            ],
            'submitted_at' => now(),
        ]);

        $category = DemoCategory::create([
            'name' => 'Webs',
        ]);

        $recommendedDemo = Demo::create([
            'demo_category_id' => $category->id,
            'name' => 'Demo destacada',
            'link' => 'https://demo.test/destacada',
        ]);

        $client->demos()->sync([
            $recommendedDemo->id => ['is_recommended' => true],
        ]);

        $this->actingAs($admin)
            ->get('/admin/clientes')
            ->assertOk()
            ->assertSee('Clientes')
            ->assertSee('Cliente Manual')
            ->assertSee('Proyecto Manual')
            ->assertSee('Demo destacada');
    }

    public function test_admin_can_access_demo_resources(): void
    {
        $admin = User::factory()->admin()->create();
        $category = DemoCategory::create([
            'name' => 'Webs',
        ]);

        Demo::create([
            'demo_category_id' => $category->id,
            'name' => 'Demo corporativa',
            'link' => 'https://demo.test/corporativa',
        ]);

        $this->actingAs($admin)
            ->get('/admin/categorias-demos')
            ->assertOk()
            ->assertSee('Categorias demos')
            ->assertSee('Webs');

        $this->actingAs($admin)
            ->get('/admin/demos')
            ->assertOk()
            ->assertSee('Demos')
            ->assertSee('Demo corporativa');
    }

    public function test_admin_can_access_budgets_resource(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->create([
            'name' => 'Cliente Facturacion',
        ]);

        Budget::create([
            'user_id' => $client->id,
            'title' => 'Presupuesto inicial',
            'pdf_path' => 'budgets/demo.pdf',
            'status' => 'pendiente',
            'issued_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/budgets')
            ->assertOk()
            ->assertSee('Presupuestos')
            ->assertSee('Presupuesto inicial')
            ->assertSee('Cliente Facturacion');
    }

    public function test_admin_can_access_invoices_resource(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->create([
            'name' => 'Cliente Facturas',
        ]);

        Invoice::create([
            'user_id' => $client->id,
            'title' => 'Factura marzo',
            'pdf_path' => 'invoices/marzo.pdf',
            'status' => 'no_pagada',
            'issued_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/invoices')
            ->assertOk()
            ->assertSee('Facturas')
            ->assertSee('Factura marzo')
            ->assertSee('Cliente Facturas');
    }

    public function test_admin_can_access_development_requests_resource(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->create([
            'name' => 'Cliente Desarrollo',
        ]);

        DevelopmentRequest::create([
            'user_id' => $client->id,
            'subject' => 'Ajuste de cabecera',
            'priority' => 'alta',
            'status' => 'abierto',
            'last_message_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/development-requests')
            ->assertOk()
            ->assertSee('Edit Area')
            ->assertSee('Ajuste de cabecera')
            ->assertSee('Cliente Desarrollo');
    }
}
