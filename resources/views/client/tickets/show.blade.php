@php
    $statusLabels = [
        'abierto' => 'Abierto',
        'en_proceso' => 'En proceso',
        'respondido' => 'Respondido',
        'cerrado' => 'Cerrado',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Soporte 24/7</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">{{ $ticket->subject }}</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Ticket #{{ $ticket->id }} / {{ ucfirst($ticket->category) }} / Estado {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                    </p>
                </div>
                <a href="{{ route('client.tickets.index') }}" class="btn-secondary">Volver a tickets</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="rounded-[24px] border border-lime-300/20 bg-lime-300/10 px-4 py-3 text-sm font-medium text-lime-100">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1.18fr_0.82fr]">
        <section class="panel-premium p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-3xl font-semibold text-white">Conversacion</h2>
                <span class="brand-badge">{{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}</span>
            </div>

            <div class="mt-8 grid gap-4">
                @foreach ($ticket->messages->where('is_internal', false) as $message)
                    <div class="{{ $message->sender_type === 'cliente' ? 'ticket-bubble-client' : 'ticket-bubble-admin' }}">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold {{ $message->sender_type === 'cliente' ? 'text-cyan-100' : 'text-white' }}">
                                {{ $message->sender_type === 'cliente' ? 'Tu mensaje' : 'Equipo DSH' }}
                            </p>
                            <p class="text-xs text-slate-400">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 {{ $message->sender_type === 'cliente' ? 'text-cyan-50' : 'text-slate-200' }}">{{ $message->message }}</p>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('client.tickets.reply', $ticket) }}" class="mt-8 border-t border-white/10 pt-6">
                @csrf
                <label class="field-label">Responder</label>
                <textarea name="message" class="field-input min-h-40" required>{{ old('message') }}</textarea>
                @error('message') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn-primary">Enviar respuesta</button>
                </div>
            </form>
        </section>

        <aside class="grid gap-6">
            <article class="panel-premium p-6">
                <p class="section-kicker">Estado</p>
                <div class="mt-5 space-y-4 text-sm">
                    <div class="stat-strip"><span class="text-slate-300">Estado actual</span><span class="font-semibold text-white">{{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}</span></div>
                    <div class="stat-strip"><span class="text-slate-300">Prioridad</span><span class="font-semibold text-white">{{ ucfirst($ticket->priority) }}</span></div>
                    <div class="stat-strip"><span class="text-slate-300">Ultimo movimiento</span><span class="font-semibold text-white">{{ optional($ticket->last_message_at)->format('d/m/Y H:i') }}</span></div>
                </div>
            </article>
        </aside>
    </div>
</x-app-layout>
