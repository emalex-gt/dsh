<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetDecisionRequest;
use App\Models\Budget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientBudgetController extends Controller
{
    public function index(Request $request): View
    {
        $budgets = $request->user()->budgets()->latest('issued_at')->latest('id')->get();

        return view('client.budgets.index', [
            'budgets' => $budgets,
        ]);
    }

    public function show(Request $request, Budget $budget): View
    {
        abort_unless($budget->user_id === $request->user()->id, 403);

        return view('client.budgets.show', [
            'budget' => $budget,
        ]);
    }

    public function file(Request $request, Budget $budget): StreamedResponse
    {
        abort_unless($budget->user_id === $request->user()->id, 403);

        return Storage::disk('local')->response($budget->pdf_path, basename($budget->pdf_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($budget->pdf_path).'"',
        ]);
    }

    public function accept(BudgetDecisionRequest $request, Budget $budget): RedirectResponse
    {
        abort_unless($budget->user_id === $request->user()->id, 403);

        $budget->update([
            'status' => 'aprobado',
            'accepted_at' => now(),
            'responded_at' => now(),
            'accepted_name' => $request->string('accepted_name')->toString(),
            'accepted_ip' => $request->ip(),
            'accepted_user_agent' => (string) $request->userAgent(),
            'client_notes' => $request->filled('client_notes') ? $request->string('client_notes')->toString() : null,
        ]);

        return redirect()
            ->route('client.budgets.show', $budget)
            ->with('status', 'Presupuesto aprobado correctamente.');
    }

    public function requestChanges(BudgetDecisionRequest $request, Budget $budget): RedirectResponse
    {
        abort_unless($budget->user_id === $request->user()->id, 403);

        $budget->update([
            'status' => 'cambios_solicitados',
            'accepted_at' => null,
            'responded_at' => now(),
            'accepted_name' => $request->string('accepted_name')->toString(),
            'accepted_ip' => $request->ip(),
            'accepted_user_agent' => (string) $request->userAgent(),
            'client_notes' => $request->filled('client_notes') ? $request->string('client_notes')->toString() : null,
        ]);

        return redirect()
            ->route('client.budgets.show', $budget)
            ->with('status', 'Solicitud de cambios enviada correctamente.');
    }
}
