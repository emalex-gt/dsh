@php($estimate = data_get($brief->data, 'technical_estimate', []))
<x-guest-layout :brief-mode="true">
    <section class="panel-premium px-6 py-14 sm:px-10 lg:px-14">
        <div class="mx-auto max-w-4xl">
            <span class="brand-badge">Estimacion inicial</span>
            <h1 class="mt-5 text-4xl font-semibold text-white">Revisa tu seleccion</h1>
            <p class="mt-4 text-slate-300">Esta estimacion se ha calculado con los servicios y opciones que has seleccionado.</p>
            <div class="mt-8 space-y-4">
                @foreach ((array) data_get($brief->data, 'selected_service_configurations', []) as $service)
                    <article class="rounded-2xl border border-white/10 bg-slate-900/60 p-5">
                        <h2 class="font-semibold text-white">{{ data_get($service, 'subcategory_name') }} / {{ data_get($service, 'service_name') }}</h2>
                        <ul class="mt-3 space-y-1 text-sm text-slate-300">
                            @foreach ((array) data_get($service, 'items', []) as $item)<li>{{ data_get($item, 'item_name') }}: {{ data_get($item, 'option_name') }}</li>@endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
            <div class="mt-8 rounded-2xl border border-cyan-300/30 bg-cyan-300/10 p-6">
                <p class="text-sm uppercase tracking-[0.2em] text-cyan-200">Estimacion inicial</p>
                <p class="mt-2 text-4xl font-semibold text-white">{{ number_format((float) data_get($estimate, 'budget.estimated_total_before_tax', 0), 2, ',', '.') }} €</p>
                <p class="mt-2 text-sm text-slate-300">Importes sin IVA.</p>
            </div>
            <form class="mt-8 space-y-4" method="POST" action="{{ route('brief.confirm', $token) }}">
                @csrf
                <label class="field-label">Nombre completo para confirmar</label>
                <input class="field-input" name="confirmed_name" value="{{ old('confirmed_name', data_get($brief->data, 'contact_name')) }}" required>
                <label class="flex gap-3 text-sm text-slate-300"><input type="checkbox" name="accept_terms" value="1" required> Confirmo que he revisado y acepto esta estimacion inicial.</label>
                <p class="text-sm text-slate-400">Al confirmar, generaremos tu acceso al area privada y registraremos este presupuesto para su seguimiento.</p>
                <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('brief.adjust', $token) }}" class="btn-secondary">Ajustar servicios</a>
                <button class="btn-primary" type="submit">Confirmar estimacion y crear mi acceso</button>
                </div>
            </form>
        </div>
    </section>
</x-guest-layout>
