<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Facturacion</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">{{ $budget->title }}</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Revisa el PDF completo y, si estas conforme, apruebalo desde este mismo panel.
                    </p>
                </div>
                <a href="{{ route('client.budgets.index') }}" class="btn-secondary">Volver a presupuestos</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="rounded-[24px] border border-lime-300/20 bg-lime-300/10 px-4 py-3 text-sm font-medium text-lime-100">{{ session('status') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <section class="panel-premium overflow-hidden p-3 sm:p-4">
            <div class="overflow-hidden rounded-[24px] border border-white/10 bg-white">
                <iframe src="{{ route('client.budgets.file', $budget) }}" class="h-[75vh] w-full border-0" title="PDF de {{ $budget->title }}"></iframe>
            </div>
        </section>

        <aside class="grid gap-6">
            <article class="panel-premium p-6">
                <p class="section-kicker">Estado</p>
                <div class="mt-5 space-y-4 text-sm">
                    <div class="stat-strip"><span class="text-slate-300">Estado actual</span><span class="font-semibold text-white">{{ \App\Models\Budget::statusOptions()[$budget->status] ?? ucfirst($budget->status) }}</span></div>
                    <div class="stat-strip"><span class="text-slate-300">Emitido</span><span class="font-semibold text-white">{{ optional($budget->issued_at)->format('d/m/Y H:i') ?: '-' }}</span></div>
                    <div class="stat-strip"><span class="text-slate-300">Respondido</span><span class="font-semibold text-white">{{ optional($budget->responded_at)->format('d/m/Y H:i') ?: '-' }}</span></div>
                </div>
            </article>

            @if ($budget->status === 'aprobado')
                <article class="panel-premium p-6">
                    <p class="section-kicker">Constancia</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Presupuesto aprobado</h2>
                    <p class="mt-4 text-sm leading-8 text-slate-300">Este presupuesto fue aprobado digitalmente por {{ $budget->accepted_name }} el {{ optional($budget->accepted_at)->format('d/m/Y H:i') }}.</p>
                    @if ($budget->client_notes)
                        <p class="mt-4 text-sm leading-8 text-slate-300">Observaciones: {{ $budget->client_notes }}</p>
                    @endif
                    <p class="mt-4 text-xs leading-7 text-slate-500">Registro interno: IP {{ $budget->accepted_ip ?: '-' }}</p>
                </article>
            @elseif ($budget->status === 'cambios_solicitados')
                <article class="panel-premium p-6">
                    <p class="section-kicker">Respuesta enviada</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Cambios solicitados</h2>
                    <p class="mt-4 text-sm leading-8 text-slate-300">{{ $budget->accepted_name }} solicito ajustes el {{ optional($budget->responded_at)->format('d/m/Y H:i') }}.</p>
                    @if ($budget->client_notes)
                        <p class="mt-4 text-sm leading-8 text-slate-300">Observaciones: {{ $budget->client_notes }}</p>
                    @endif
                    <p class="mt-4 text-xs leading-7 text-slate-500">Registro interno: IP {{ $budget->accepted_ip ?: '-' }}</p>
                </article>
            @else
                <article class="panel-premium p-6">
                    <p class="section-kicker">Respuesta del cliente</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Responder presupuesto</h2>
                    <p class="mt-4 text-sm leading-8 text-slate-300">Puedes aprobar este presupuesto o solicitar cambios. En ambos casos dejaremos constancia de nombre, fecha, IP y observaciones.</p>

                    <form method="POST" action="{{ route('client.budgets.accept', $budget) }}" class="mt-6 space-y-5">
                        @csrf
                        <div>
                            <label class="field-label">Nombre completo</label>
                            <input name="accepted_name" class="field-input" value="{{ old('accepted_name', auth()->user()->name) }}" required>
                            @error('accepted_name') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="field-label">Observaciones del cliente</label>
                            <textarea name="client_notes" class="field-input min-h-32">{{ old('client_notes') }}</textarea>
                            @error('client_notes') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                        </div>

                        <label class="flex items-start gap-3 rounded-[24px] border border-white/10 bg-white/5 px-4 py-4 text-sm leading-7 text-slate-300">
                            <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-white/20 bg-slate-950 text-lime-300 focus:ring-lime-300/40" required>
                            <span>Confirmo que he revisado el PDF y que esta respuesta quedara registrada con fecha, IP y usuario.</span>
                        </label>
                        @error('accept_terms') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror

                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="submit" class="btn-primary w-full">Aprobar presupuesto</button>
                            <button type="submit" formaction="{{ route('client.budgets.request-changes', $budget) }}" class="btn-secondary w-full">Solicitar cambios</button>
                        </div>
                    </form>
                </article>
            @endif
        </aside>
    </div>
</x-app-layout>
