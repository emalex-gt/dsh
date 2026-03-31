<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Desarrollo</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">{{ $developmentRequest->subject }}</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Sigue esta solicitud de desarrollo y centraliza aqui todo el intercambio con el equipo.
                    </p>
                </div>
                <a href="{{ route('client.development.requests.index') }}" class="btn-secondary">Volver a Edit Area</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="rounded-[24px] border border-lime-300/20 bg-lime-300/10 px-4 py-3 text-sm font-medium text-lime-100">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
        <aside class="grid gap-6">
            <article class="panel-premium p-6">
                <p class="section-kicker">Estado</p>
                <div class="mt-5 space-y-4 text-sm">
                    <div class="stat-strip"><span class="text-slate-300">Estado actual</span><span class="font-semibold text-white">{{ \App\Models\DevelopmentRequest::statusOptions()[$developmentRequest->status] ?? ucfirst($developmentRequest->status) }}</span></div>
                    <div class="stat-strip"><span class="text-slate-300">Prioridad</span><span class="font-semibold text-white">{{ \App\Models\DevelopmentRequest::priorityOptions()[$developmentRequest->priority] ?? ucfirst($developmentRequest->priority) }}</span></div>
                    <div class="stat-strip"><span class="text-slate-300">Ultima actualizacion</span><span class="font-semibold text-white">{{ optional($developmentRequest->last_message_at)->format('d/m/Y H:i') ?: '-' }}</span></div>
                </div>
            </article>

            <article class="panel-premium p-6">
                <p class="section-kicker">Responder</p>
                <form method="POST" action="{{ route('client.development.requests.reply', $developmentRequest) }}" class="mt-4 space-y-5">
                    @csrf
                    <div>
                        <label class="field-label">Mensaje</label>
                        <textarea name="message" class="field-input min-h-32" required>{{ old('message') }}</textarea>
                        @error('message') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn-primary w-full">Enviar actualizacion</button>
                </form>
            </article>
        </aside>

        <section class="panel-premium p-6 sm:p-8">
            <p class="section-kicker">Conversacion</p>
            <div class="mt-6 grid gap-4">
                @foreach ($developmentRequest->messages as $message)
                    <article class="rounded-[28px] border {{ $message->sender_type === 'admin' ? 'border-cyan-300/20 bg-cyan-300/10' : 'border-white/10 bg-white/5' }} p-5">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold text-white">{{ $message->sender_type === 'admin' ? 'Equipo DSH' : 'Cliente' }}</p>
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <p class="mt-4 text-sm leading-8 text-slate-200">{{ $message->message }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
