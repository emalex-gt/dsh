<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketReplyRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientTicketController extends Controller
{
    public function index(Request $request): View
    {
        return view('client.tickets.index', [
            'tickets' => $request->user()
                ->tickets()
                ->with('messages')
                ->latest('last_message_at')
                ->latest('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('client.tickets.create');
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $brief = $request->user()->briefs()->latest('submitted_at')->latest('id')->first();

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'brief_id' => $brief?->id,
            'subject' => $request->string('subject')->toString(),
            'category' => $request->string('category')->toString(),
            'priority' => $request->string('priority')->toString(),
            'status' => 'abierto',
            'last_message_at' => now(),
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'sender_type' => 'cliente',
            'message' => $request->string('message')->toString(),
            'is_internal' => false,
        ]);

        return redirect()
            ->route('client.tickets.show', $ticket)
            ->with('status', 'Ticket creado correctamente.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        $ticket->load(['messages.user']);

        return view('client.tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(StoreTicketReplyRequest $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'sender_type' => 'cliente',
            'message' => $request->string('message')->toString(),
            'is_internal' => false,
        ]);

        $ticket->update([
            'status' => 'abierto',
            'last_message_at' => now(),
        ]);

        return redirect()
            ->route('client.tickets.show', $ticket)
            ->with('status', 'Respuesta enviada.');
    }
}
