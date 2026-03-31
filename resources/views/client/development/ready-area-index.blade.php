<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Desarrollo</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">Ready Area</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Sigue las respuestas del equipo y revisa si cada solicitud esta en progreso o finalizada.
                    </p>
                </div>
                <a href="{{ route('client.development.requests.index') }}" class="btn-secondary">Ir a Edit Area</a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6">
        <nav class="panel-premium flex flex-col gap-3 p-4 sm:flex-row sm:flex-wrap">
            <a href="{{ route('client.development.show', 'hosting') }}" class="nav-pill text-center">Hosting</a>
            <a href="{{ route('client.development.show', 'dominio') }}" class="nav-pill text-center">Dominio</a>
            <a href="{{ route('client.development.show', 'email') }}" class="nav-pill text-center">Email</a>
            <a href="{{ route('client.development.requests.index') }}" class="nav-pill text-center">Edit Area</a>
            <a href="{{ route('client.development.ready.index') }}" class="nav-pill-active text-center">Ready Area</a>
        </nav>

        @if ($requests->isEmpty())
            <div class="panel-premium p-10 text-center">
                <h2 class="text-3xl font-semibold text-white">No hay solicitudes en progreso o finalizadas</h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-8 text-slate-300">Las respuestas del equipo apareceran aqui una vez comiencen a trabajar sobre tus solicitudes.</p>
            </div>
        @else
            <div class="grid gap-5">
                @foreach ($requests as $developmentRequest)
                    <article class="panel-premium p-6 sm:p-8">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <span class="brand-badge">{{ \App\Models\DevelopmentRequest::statusOptions()[$developmentRequest->status] ?? ucfirst($developmentRequest->status) }}</span>
                                <h2 class="mt-4 text-3xl font-semibold text-white">{{ $developmentRequest->subject }}</h2>
                                <p class="mt-4 text-sm leading-7 text-slate-400">Ultima actualizacion {{ optional($developmentRequest->last_message_at)->format('d/m/Y H:i') ?: '-' }}</p>
                            </div>
                            <a href="{{ route('client.development.requests.show', $developmentRequest) }}" class="btn-secondary">Ver respuesta</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
