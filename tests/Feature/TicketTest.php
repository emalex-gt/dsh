<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_ticket(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('client.tickets.store'), [
            'subject' => 'Necesito ayuda con acceso al panel',
            'category' => 'acceso',
            'priority' => 'media',
            'message' => 'No puedo entrar correctamente a una seccion del panel.',
        ]);

        $ticket = Ticket::first();

        $response->assertRedirect(route('client.tickets.show', $ticket));
        $this->assertDatabaseHas('tickets', [
            'user_id' => $user->id,
            'subject' => 'Necesito ayuda con acceso al panel',
            'status' => 'abierto',
        ]);
        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'sender_type' => 'cliente',
        ]);
    }

    public function test_client_can_reply_to_own_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Ticket demo',
            'category' => 'tecnico',
            'priority' => 'media',
            'status' => 'respondido',
            'last_message_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('client.tickets.reply', $ticket), [
            'message' => 'Aporto mas contexto sobre la incidencia.',
        ]);

        $response->assertRedirect(route('client.tickets.show', $ticket));
        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'sender_type' => 'cliente',
            'message' => 'Aporto mas contexto sobre la incidencia.',
        ]);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'abierto',
        ]);
    }

    public function test_client_cannot_view_other_users_ticket(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $ticket = Ticket::create([
            'user_id' => $owner->id,
            'subject' => 'Ticket privado',
            'category' => 'tecnico',
            'priority' => 'media',
            'status' => 'abierto',
            'last_message_at' => now(),
        ]);

        $this->actingAs($other)
            ->get(route('client.tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_client_ticket_page_hides_internal_admin_notes(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Ticket demo',
            'category' => 'tecnico',
            'priority' => 'media',
            'status' => 'abierto',
            'last_message_at' => now(),
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'admin',
            'message' => 'Nota interna',
            'is_internal' => true,
        ]);

        $this->actingAs($user)
            ->get(route('client.tickets.show', $ticket))
            ->assertOk()
            ->assertDontSee('Nota interna');
    }
}
