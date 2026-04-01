<?php

namespace Tests\Feature;

use App\Models\ClientEmailAccount;
use App\Models\DevelopmentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevelopmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_sees_no_assignment_messages_when_nothing_is_loaded(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('client.development.show', 'hosting'))
            ->assertOk()
            ->assertSee('No tiene asignado hosting.');

        $this->actingAs($user)
            ->get(route('client.development.show', 'dominio'))
            ->assertOk()
            ->assertSee('No tiene asignado dominio.');

        $this->actingAs($user)
            ->get(route('client.development.show', 'email'))
            ->assertOk()
            ->assertSee('No tiene asignado correo electronico.');
    }

    public function test_client_can_view_assigned_development_data(): void
    {
        $user = User::factory()->create([
            'hosting_link' => 'https://hosting.demo.test',
            'hosting_username' => 'cliente_host',
            'hosting_password' => 'host-pass',
            'hosting_expires_at' => '2026-12-01',
            'hosting_price' => '129 â‚¬ / ano',
            'domain_name' => 'www.demo.test',
            'domain_expires_at' => '2026-11-20',
            'domain_price' => '18 â‚¬ / ano',
        ]);

        ClientEmailAccount::create([
            'user_id' => $user->id,
            'label' => 'Soporte',
            'email' => 'soporte@demo.test',
            'access_link' => 'https://mail.demo.test',
            'username' => 'soporte@demo.test',
            'password' => 'mail-pass',
        ]);

        $this->actingAs($user)
            ->get(route('client.development.show', 'hosting'))
            ->assertOk()
            ->assertSee('https://hosting.demo.test')
            ->assertSee('cliente_host')
            ->assertSee('129 â‚¬ / ano');

        $this->actingAs($user)
            ->get(route('client.development.show', 'dominio'))
            ->assertOk()
            ->assertSee('www.demo.test')
            ->assertSee('18 â‚¬ / ano');

        $this->actingAs($user)
            ->get(route('client.development.show', 'email'))
            ->assertOk()
            ->assertSee('soporte@demo.test')
            ->assertSee('https://mail.demo.test');
    }

    public function test_client_can_create_and_track_development_request(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('client.development.requests.store'), [
            'subject' => 'Cambiar el hero principal',
            'priority' => 'alta',
            'message' => 'Necesitamos actualizar el hero principal y ajustar el copy del boton.',
        ]);

        $request = DevelopmentRequest::first();

        $response->assertRedirect(route('client.development.requests.show', $request));

        $this->actingAs($user)
            ->get(route('client.development.requests.index'))
            ->assertOk()
            ->assertSee('Cambiar el hero principal');
    }

    public function test_ready_area_lists_requests_in_progress_or_finished(): void
    {
        $user = User::factory()->create();

        DevelopmentRequest::create([
            'user_id' => $user->id,
            'subject' => 'Bloque de testimonios',
            'priority' => 'media',
            'status' => 'en_progreso',
            'last_message_at' => now(),
        ]);

        DevelopmentRequest::create([
            'user_id' => $user->id,
            'subject' => 'Cambio invisible',
            'priority' => 'baja',
            'status' => 'abierto',
            'last_message_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('client.development.ready.index'))
            ->assertOk()
            ->assertSee('Bloque de testimonios')
            ->assertDontSee('Cambio invisible');
    }

    public function test_edit_area_hides_finished_requests(): void
    {
        $user = User::factory()->create();

        DevelopmentRequest::create([
            'user_id' => $user->id,
            'subject' => 'Solicitud abierta',
            'priority' => 'media',
            'status' => 'abierto',
            'last_message_at' => now(),
        ]);

        DevelopmentRequest::create([
            'user_id' => $user->id,
            'subject' => 'Solicitud finalizada',
            'priority' => 'alta',
            'status' => 'finalizado',
            'last_message_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('client.development.requests.index'))
            ->assertOk()
            ->assertSee('Solicitud abierta')
            ->assertDontSee('Solicitud finalizada');
    }

    public function test_expired_hosting_and_domain_show_global_notifications(): void
    {
        $user = User::factory()->create([
            'hosting_expires_at' => now()->subDay()->toDateString(),
            'domain_expires_at' => now()->subDays(2)->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('El hosting de tu proyecto esta vencido')
            ->assertSee('El dominio de tu proyecto esta vencido')
            ->assertSee(route('client.budgets.index'))
            ->assertSee(route('client.development.show', 'hosting'))
            ->assertSee(route('client.development.show', 'dominio'));
    }
}

