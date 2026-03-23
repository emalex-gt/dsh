<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_view_budget_index(): void
    {
        $user = User::factory()->create();

        Budget::create([
            'user_id' => $user->id,
            'title' => 'Presupuesto web corporativa',
            'pdf_path' => 'budgets/presupuesto-demo.pdf',
            'status' => 'pendiente',
            'issued_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('client.budgets.index'))
            ->assertOk()
            ->assertSee('Presupuestos')
            ->assertSee('Presupuesto web corporativa');
    }

    public function test_client_can_approve_own_budget_with_notes(): void
    {
        $user = User::factory()->create();
        $budget = Budget::create([
            'user_id' => $user->id,
            'title' => 'Presupuesto ecommerce',
            'pdf_path' => 'budgets/presupuesto-demo.pdf',
            'status' => 'pendiente',
            'issued_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('client.budgets.accept', $budget), [
            'accepted_name' => 'Ana Cliente',
            'client_notes' => 'Aprobado con las condiciones revisadas.',
            'accept_terms' => '1',
        ]);

        $response->assertRedirect(route('client.budgets.show', $budget));
        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'status' => 'aprobado',
            'accepted_name' => 'Ana Cliente',
            'client_notes' => 'Aprobado con las condiciones revisadas.',
        ]);
    }

    public function test_client_can_request_changes_on_budget(): void
    {
        $user = User::factory()->create();
        $budget = Budget::create([
            'user_id' => $user->id,
            'title' => 'Presupuesto ajustes',
            'pdf_path' => 'budgets/presupuesto-demo.pdf',
            'status' => 'pendiente',
            'issued_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('client.budgets.request-changes', $budget), [
            'accepted_name' => 'Ana Cliente',
            'client_notes' => 'Necesitamos ajustar alcance y fases de pago.',
            'accept_terms' => '1',
        ]);

        $response->assertRedirect(route('client.budgets.show', $budget));
        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'status' => 'cambios_solicitados',
            'accepted_name' => 'Ana Cliente',
            'client_notes' => 'Necesitamos ajustar alcance y fases de pago.',
        ]);
    }

    public function test_client_cannot_view_other_users_budget(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $budget = Budget::create([
            'user_id' => $owner->id,
            'title' => 'Presupuesto privado',
            'pdf_path' => 'budgets/presupuesto-demo.pdf',
            'status' => 'pendiente',
            'issued_at' => now(),
        ]);

        $this->actingAs($other)
            ->get(route('client.budgets.show', $budget))
            ->assertForbidden();
    }
}
