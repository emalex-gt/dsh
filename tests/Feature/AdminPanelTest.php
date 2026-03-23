<?php

namespace Tests\Feature;

use App\Models\Brief;
use App\Models\Budget;
use App\Models\Demo;
use App\Models\DemoCategory;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

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
}
