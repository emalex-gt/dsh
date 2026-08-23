@php
    $briefData = $brief?->data ?? [];
    $value = fn (string $key, mixed $default = '-') => data_get($briefData, $key, $default);
    $list = function (string $key) use ($briefData): string {
        $items = array_filter((array) data_get($briefData, $key, []));

        return $items === [] ? '-' : collect($items)->implode(', ');
    };
    $money = function (mixed $raw): string {
        if (blank($raw)) {
            return '-';
        }

        $string = trim((string) $raw);

        if (is_numeric($string)) {
            return number_format((float) $string, 0, ',', '.').' €';
        }

        $normalized = trim(str_replace(['EUR', '€'], '', $string));

        return $normalized === '' ? '-' : $normalized.' €';
    };
    $serviceLabels = collect((array) data_get($briefData, 'selected_services', []))
        ->map(fn (string $service) => [
            'web' => 'Web',
            'apps' => 'Apps',
            'store' => 'Tienda online',
            'design' => 'UX / UI',
            'systems' => 'Sistemas',
        ][$service] ?? $service)
        ->implode(', ');
    $selectedServiceItems = collect((array) data_get($briefData, 'selected_service_items', []));
    $technicalEstimate = (array) data_get($briefData, 'technical_estimate', []);
    $technicalBudget = (array) data_get($technicalEstimate, 'budget', []);
    $technicalEffort = (array) data_get($technicalEstimate, 'effort', []);
    $estimateMoney = fn (mixed $amount): string => is_numeric($amount)
        ? number_format((float) $amount, 2, ',', '.').' EUR'
        : '-';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Mi brief</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">{{ $value('brand_name', $value('legal_name', 'Tu proyecto')) }}</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Aqui tienes la version consolidada de la informacion compartida para tu proyecto. Este espacio es de consulta y referencia.
                    </p>
                </div>
                <a href="{{ route('client.data.show') }}" class="btn-secondary">Volver a Mis Datos</a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6">
        <section class="panel-premium p-6 sm:p-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="metric-card">
                    <p class="section-kicker">Empresa</p>
                    <p class="mt-3 text-xl font-semibold text-white">{{ $value('brand_name', $value('legal_name')) }}</p>
                    <p class="mt-2 text-sm text-slate-400">{{ $value('legal_name') }}</p>
                </div>
                <div class="metric-card">
                    <p class="section-kicker">Contacto</p>
                    <p class="mt-3 text-xl font-semibold text-white">{{ $value('contact_name') }}</p>
                    <p class="mt-2 text-sm text-slate-400">{{ $value('contact_email') }}</p>
                </div>
                <div class="metric-card">
                    <p class="section-kicker">Servicios</p>
                    <p class="mt-3 text-xl font-semibold text-white">{{ $value('selected_service_summary', $serviceLabels ?: '-') }}</p>
                    <p class="mt-2 text-sm text-slate-400">Alcance de referencia</p>
                </div>
                <div class="metric-card">
                    <p class="section-kicker">Presupuesto</p>
                    <p class="mt-3 text-xl font-semibold text-white">{{ $estimateMoney(data_get($technicalBudget, 'technical_base')) }}</p>
                    <p class="mt-2 text-sm text-slate-400">Base tecnica estimada</p>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="panel-premium p-6">
                <h2 class="text-2xl font-semibold text-white">Empresa y contacto</h2>
                <dl class="mt-6 grid gap-4 md:grid-cols-2">
                    <div><dt class="section-kicker">Pais</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('country') }}</dd></div>
                    <div><dt class="section-kicker">Ciudad</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('city') }}</dd></div>
                    <div><dt class="section-kicker">Cargo</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('contact_role') }}</dd></div>
                    <div><dt class="section-kicker">Telefono</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('contact_phone') }}</dd></div>
                    <div class="md:col-span-2"><dt class="section-kicker">Sitio web</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('website') }}</dd></div>
                    <div class="md:col-span-2"><dt class="section-kicker">Redes</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('social_links') }}</dd></div>
                </dl>
            </article>

            <article class="panel-premium p-6">
                <h2 class="text-2xl font-semibold text-white">Objetivo comercial</h2>
                <dl class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2"><dt class="section-kicker">Objetivos</dt><dd class="mt-2 text-sm text-slate-200">{{ $list('project_objectives') }}</dd></div>
                    <div><dt class="section-kicker">Horizonte</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('results_timeframe') }}</dd></div>
                    <div><dt class="section-kicker">Modelo</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('business_model') }}</dd></div>
                    <div><dt class="section-kicker">Mercado</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('main_market') }}</dd></div>
                    <div><dt class="section-kicker">Ticket medio</dt><dd class="mt-2 text-sm text-slate-200">{{ $money($value('average_ticket', null)) }}</dd></div>
                    <div class="md:col-span-2"><dt class="section-kicker">Cliente ideal</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('ideal_customer') }}</dd></div>
                </dl>
            </article>

            <article class="panel-premium p-6">
                <h2 class="text-2xl font-semibold text-white">Servicios y requerimientos</h2>
                <dl class="mt-6 space-y-4">
                    <div>
                        <dt class="section-kicker">Servicios solicitados</dt>
                        <dd class="mt-2 text-sm text-slate-200">{{ $value('selected_service_summary', $serviceLabels ?: '-') }}</dd>
                    </div>
                    @if ($selectedServiceItems->isNotEmpty())
                        <div>
                            <dt class="section-kicker">Configuracion elegida</dt>
                            <dd class="mt-3 space-y-3 text-sm text-slate-200">
                                @foreach ($selectedServiceItems as $item)
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <p class="font-semibold text-white">{{ data_get($item, 'item_name') }}</p>
                                        <p class="mt-1 text-slate-300">{{ data_get($item, 'option_name') }}</p>
                                        @if (count((array) data_get($item, 'price_summary', [])))
                                            <div class="mt-2 space-y-1">
                                                @foreach ((array) data_get($item, 'price_summary', []) as $priceLine)
                                                    <p class="text-xs uppercase tracking-[0.18em] text-cyan-200">{{ $priceLine }}</p>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </dd>
                        </div>
                    @else
                        <div>
                            <dt class="section-kicker">Detalle</dt>
                            <dd class="mt-2 text-sm text-slate-200">Sin configuracion detallada registrada.</dd>
                        </div>
                    @endif
                </dl>
            </article>

            <article class="panel-premium p-6">
                <h2 class="text-2xl font-semibold text-white">Estimacion tecnica</h2>
                @if (data_get($technicalEstimate, 'status') === 'calculated')
                    <dl class="mt-6 grid gap-4 md:grid-cols-2">
                        <div><dt class="section-kicker">Escenario</dt><dd class="mt-2 text-sm font-semibold text-white">{{ data_get($technicalEstimate, 'technical_service.scenario') }}</dd></div>
                        <div><dt class="section-kicker">Biblioteca</dt><dd class="mt-2 text-sm text-slate-200">{{ data_get($technicalEstimate, 'library.name') }}</dd></div>
                        <div><dt class="section-kicker">Equipo previsto</dt><dd class="mt-2 text-sm text-slate-200">{{ data_get($technicalEffort, 'people') }} personas, {{ data_get($technicalEffort, 'days') }} dias</dd></div>
                        <div><dt class="section-kicker">Carga estimada</dt><dd class="mt-2 text-sm text-slate-200">{{ data_get($technicalEffort, 'subtotal_hours') }} horas</dd></div>
                        <div><dt class="section-kicker">Base tecnica</dt><dd class="mt-2 text-sm font-semibold text-white">{{ $estimateMoney(data_get($technicalBudget, 'technical_base')) }}</dd></div>
                        <div><dt class="section-kicker">Total con seleccion</dt><dd class="mt-2 text-sm font-semibold text-lime-200">{{ $estimateMoney(data_get($technicalBudget, 'estimated_total')) }} + IVA</dd></div>
                    </dl>
                    @if (count((array) data_get($technicalEstimate, 'selected_option_charges', [])) || count((array) data_get($technicalEstimate, 'selected_extra_fees', [])))
                        <div class="mt-6 border-t border-white/10 pt-5">
                            <p class="section-kicker">Cargos incluidos</p>
                            <div class="mt-3 space-y-2 text-sm text-slate-300">
                                @foreach ((array) data_get($technicalEstimate, 'selected_option_charges', []) as $charge)
                                    <p>{{ data_get($charge, 'item') }}: {{ data_get($charge, 'option') }} - {{ $estimateMoney(data_get($charge, 'amount')) }}</p>
                                @endforeach
                                @foreach ((array) data_get($technicalEstimate, 'selected_extra_fees', []) as $fee)
                                    <p>{{ data_get($fee, 'name') }} - {{ $estimateMoney(data_get($fee, 'amount')) }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <p class="mt-4 text-sm leading-7 text-slate-300">{{ data_get($technicalEstimate, 'reason', 'La estimacion tecnica se definira con el equipo.') }}</p>
                @endif
            </article>

            <article class="panel-premium p-6">
                <h2 class="text-2xl font-semibold text-white">Marca y contexto</h2>
                <dl class="mt-6 space-y-4">
                    <div><dt class="section-kicker">Tono</dt><dd class="mt-2 text-sm text-slate-200">{{ $list('brand_tone') }}</dd></div>
                    <div><dt class="section-kicker">Valores</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('brand_values') }}</dd></div>
                    <div><dt class="section-kicker">Diferenciador</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('competitive_differentiator') }}</dd></div>
                    <div><dt class="section-kicker">Competidores</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('main_competitors') }}</dd></div>
                </dl>
            </article>
        </section>
    </div>
</x-app-layout>
