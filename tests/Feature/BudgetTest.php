<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Invoice;
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

        Invoice::create([
            'user_id' => $user->id,
            'title' => 'Factura anticipo',
            'pdf_path' => 'invoices/factura-demo.pdf',
            'status' => 'no_pagada',
            'issued_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('client.budgets.index'))
            ->assertOk()
            ->assertSee('Pendientes de respuesta')
            ->assertSee('Presupuesto web corporativa')
            ->assertSee('Factura anticipo');
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

        $response->assertRedirect(route('client.budgets.index'));
        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'status' => 'aprobado',
            'accepted_name' => 'Ana Cliente',
            'client_notes' => 'Aprobado con las condiciones revisadas.',
        ]);
    }

    public function test_approved_budget_no_longer_appears_in_client_index(): void
    {
        $user = User::factory()->create();

        Budget::create([
            'user_id' => $user->id,
            'title' => 'Presupuesto ya aprobado',
            'pdf_path' => 'budgets/presupuesto-demo.pdf',
            'status' => 'aprobado',
            'issued_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('client.budgets.index'))
            ->assertOk()
            ->assertDontSee('Presupuesto ya aprobado');
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

    public function test_client_can_view_own_invoice(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'title' => 'Factura final',
            'pdf_path' => 'invoices/factura-final.pdf',
            'status' => 'pagada',
            'issued_at' => now(),
            'paid_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('client.invoices.show', $invoice))
            ->assertOk()
            ->assertSee('Factura final')
            ->assertSee('Pagada');
    }
}
