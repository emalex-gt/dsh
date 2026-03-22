@php
    $statusLabels = [
        'abierto' => 'Abierto',
        'en_proceso' => 'En proceso',
        'respondido' => 'Respondido',
        'cerrado' => 'Cerrado',
    ];
    $priorityLabels = [
        'baja' => 'Baja',
        'media' => 'Media',
        'alta' => 'Alta',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Soporte 24/7</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">Centro de tickets</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Gestiona consultas, incidencias y solicitudes desde una bandeja clara, privada y siempre accesible.
                    </p>
                </div>
                <a href="{{ route('client.tickets.create') }}" class="btn-primary">Nuevo ticket</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="rounded-[24px] border border-lime-300/20 bg-lime-300/10 px-4 py-3 text-sm font-medium text-lime-100">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6">
        <section class="grid gap-6 md:grid-cols-3">
            <article class="panel-premium p-6"><p class="section-kicker">Tickets</p><p class="metric-value">{{ $tickets->count() }}</p></article>
            <article class="panel-premium p-6"><p class="section-kicker">Abiertos</p><p class="metric-value">{{ $tickets->where('status', 'abierto')->count() }}</p></article>
            <article class="panel-premium p-6"><p class="section-kicker">En proceso</p><p class="metric-value">{{ $tickets->where('status', 'en_proceso')->count() }}</p></article>
        </section>

        <section class="panel-premium p-6 sm:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="section-kicker">Bandeja</p>
                    <h2 class="mt-3 text-3xl font-semibold text-white">Tus tickets</h2>
                </div>
                <a href="{{ route('client.tickets.create') }}" class="btn-secondary">Crear ticket</a>
            </div>

            @if ($tickets->isEmpty())
                <div class="mt-6 rounded-[28px] border border-dashed border-white/10 px-6 py-12 text-center text-slate-400">
                    Aun no tienes tickets. Cuando necesites ayuda, abre uno y lo seguiremos contigo desde aqui.
                </div>
            @else
                <div class="mt-6 grid gap-4">
                    @foreach ($tickets as $ticket)
                        <a href="{{ route('client.tickets.show', $ticket) }}" class="panel-soft block p-5 transition hover:-translate-y-0.5 hover:border-cyan-300/25 hover:bg-white/10">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                                <div class="space-y-2">
                                    <p class="text-xl font-semibold text-white">{{ $ticket->subject }}</p>
                                    <p class="text-sm text-slate-400">Ticket #{{ $ticket->id }} / {{ $priorityLabels[$ticket->priority] ?? ucfirst($ticket->priority) }} / Ultimo movimiento {{ optional($ticket->last_message_at)->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2 text-sm">
                                    <span class="rounded-full border border-white/10 px-3 py-1 text-slate-200">{{ $priorityLabels[$ticket->priority] ?? ucfirst($ticket->priority) }}</span>
                                    <span class="rounded-full border border-cyan-300/20 bg-cyan-300/10 px-3 py-1 text-cyan-100">{{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
