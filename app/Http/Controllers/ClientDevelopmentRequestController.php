<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDevelopmentReplyRequest;
use App\Http\Requests\StoreDevelopmentRequestRequest;
use App\Models\DevelopmentRequest;
use App\Models\DevelopmentRequestMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientDevelopmentRequestController extends Controller
{
    public function editAreaIndex(Request $request): View
    {
        return view('client.development.edit-area-index', [
            'requests' => $request->user()
                ->developmentRequests()
                ->with(['messages' => fn ($query) => $query->where('is_internal', false)])
                ->whereIn('status', ['abierto', 'en_progreso'])
                ->latest('last_message_at')
                ->latest('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('client.development.create-request');
    }

    public function store(StoreDevelopmentRequestRequest $request): RedirectResponse
    {
        $brief = $request->user()->briefs()->latest('submitted_at')->latest('id')->first();

        $developmentRequest = DevelopmentRequest::create([
            'user_id' => $request->user()->id,
            'brief_id' => $brief?->id,
            'subject' => $request->string('subject')->toString(),
            'priority' => $request->string('priority')->toString(),
            'status' => 'abierto',
            'last_message_at' => now(),
        ]);

        DevelopmentRequestMessage::create([
            'development_request_id' => $developmentRequest->id,
            'user_id' => $request->user()->id,
            'sender_type' => 'cliente',
            'message' => $request->string('message')->toString(),
            'is_internal' => false,
        ]);

        return redirect()
            ->route('client.development.requests.show', $developmentRequest)
            ->with('status', 'Solicitud de desarrollo creada correctamente.');
    }

    public function readyAreaIndex(Request $request): View
    {
        return view('client.development.ready-area-index', [
            'requests' => $request->user()
                ->developmentRequests()
                ->with(['messages' => fn ($query) => $query->where('is_internal', false)])
                ->whereIn('status', ['en_progreso', 'finalizado'])
                ->latest('last_message_at')
                ->latest('id')
                ->get(),
        ]);
    }

    public function show(Request $request, DevelopmentRequest $developmentRequest): View
    {
        abort_unless($developmentRequest->user_id === $request->user()->id, 403);

        $developmentRequest->load([
            'messages' => fn ($query) => $query->where('is_internal', false)->with('user'),
        ]);

        return view('client.development.request-show', [
            'developmentRequest' => $developmentRequest,
        ]);
    }

    public function reply(StoreDevelopmentReplyRequest $request, DevelopmentRequest $developmentRequest): RedirectResponse
    {
        abort_unless($developmentRequest->user_id === $request->user()->id, 403);

        DevelopmentRequestMessage::create([
            'development_request_id' => $developmentRequest->id,
            'user_id' => $request->user()->id,
            'sender_type' => 'cliente',
            'message' => $request->string('message')->toString(),
            'is_internal' => false,
        ]);

        $developmentRequest->update([
            'status' => $developmentRequest->status === 'finalizado' ? 'finalizado' : 'abierto',
            'last_message_at' => now(),
        ]);

        return redirect()
            ->route('client.development.requests.show', $developmentRequest)
            ->with('status', 'Actualizacion enviada correctamente.');
    }
}
