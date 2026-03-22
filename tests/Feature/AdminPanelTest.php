<?php

namespace Tests\Feature;

use App\Models\Brief;
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

        $this->actingAs($admin)
            ->get('/admin/clientes')
            ->assertOk()
            ->assertSee('Clientes')
            ->assertSee('Cliente Manual')
            ->assertSee('Proyecto Manual');
    }
}
