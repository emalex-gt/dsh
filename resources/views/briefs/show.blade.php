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
                <div class="metric-card"><p class="section-kicker">Empresa</p><p class="mt-3 text-xl font-semibold text-white">{{ $value('brand_name', $value('legal_name')) }}</p><p class="mt-2 text-sm text-slate-400">{{ $value('legal_name') }}</p></div>
                <div class="metric-card"><p class="section-kicker">Contacto</p><p class="mt-3 text-xl font-semibold text-white">{{ $value('contact_name') }}</p><p class="mt-2 text-sm text-slate-400">{{ $value('contact_email') }}</p></div>
                <div class="metric-card"><p class="section-kicker">Servicios</p><p class="mt-3 text-xl font-semibold text-white">{{ $serviceLabels ?: '-' }}</p><p class="mt-2 text-sm text-slate-400">Alcance de referencia</p></div>
                <div class="metric-card"><p class="section-kicker">Presupuesto</p><p class="mt-3 text-xl font-semibold text-white">{{ $money($value('investment_budget', null)) }}</p><p class="mt-2 text-sm text-slate-400">Base estimada</p></div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="panel-premium p-6"><h2 class="text-2xl font-semibold text-white">Empresa y contacto</h2><dl class="mt-6 grid gap-4 md:grid-cols-2"><div><dt class="section-kicker">Pais</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('country') }}</dd></div><div><dt class="section-kicker">Ciudad</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('city') }}</dd></div><div><dt class="section-kicker">Cargo</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('contact_role') }}</dd></div><div><dt class="section-kicker">Telefono</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('contact_phone') }}</dd></div><div class="md:col-span-2"><dt class="section-kicker">Sitio web</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('website') }}</dd></div><div class="md:col-span-2"><dt class="section-kicker">Redes</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('social_links') }}</dd></div></dl></article>
            <article class="panel-premium p-6"><h2 class="text-2xl font-semibold text-white">Objetivo comercial</h2><dl class="mt-6 grid gap-4 md:grid-cols-2"><div class="md:col-span-2"><dt class="section-kicker">Objetivos</dt><dd class="mt-2 text-sm text-slate-200">{{ $list('project_objectives') }}</dd></div><div><dt class="section-kicker">Horizonte</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('results_timeframe') }}</dd></div><div><dt class="section-kicker">Modelo</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('business_model') }}</dd></div><div><dt class="section-kicker">Mercado</dt><dd class="mt-2 text-sm text-slate-200">{{ $value('main_market') }}</dd></div><div><dt class="section-kicker">Ticket medio</dt><dd class="mt-2 text-sm text-slate-200">{{ $money($value('average_ticket', null)) }}</dd></div><div class="md:col-span-2"><dt class="section-kicker">Cliente ideal</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('ideal_customer') }}</dd></div></dl></article>
            <article class="panel-premium p-6"><h2 class="text-2xl font-semibold text-white">Servicios y requerimientos</h2><dl class="mt-6 space-y-4"><div><dt class="section-kicker">Servicios solicitados</dt><dd class="mt-2 text-sm text-slate-200">{{ $serviceLabels ?: '-' }}</dd></div><div><dt class="section-kicker">Web</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('web_pack') }}{{ $value('web_requirements') !== '-' ? "\n\n".$value('web_requirements') : '' }}</dd></div><div><dt class="section-kicker">Apps</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('apps_pack') }}{{ $value('apps_requirements') !== '-' ? "\n\n".$value('apps_requirements') : '' }}</dd></div><div><dt class="section-kicker">Tienda online</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('store_pack') }}{{ $value('store_requirements') !== '-' ? "\n\n".$value('store_requirements') : '' }}</dd></div><div><dt class="section-kicker">UX / UI</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('design_scope') }}{{ $value('design_requirements') !== '-' ? "\n\n".$value('design_requirements') : '' }}</dd></div><div><dt class="section-kicker">Sistemas</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $list('systems_type') }}{{ $value('systems_requirements') !== '-' ? "\n\n".$value('systems_requirements') : '' }}</dd></div></dl></article>
            <article class="panel-premium p-6"><h2 class="text-2xl font-semibold text-white">Marca y contexto</h2><dl class="mt-6 space-y-4"><div><dt class="section-kicker">Tono</dt><dd class="mt-2 text-sm text-slate-200">{{ $list('brand_tone') }}</dd></div><div><dt class="section-kicker">Valores</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('brand_values') }}</dd></div><div><dt class="section-kicker">Diferenciador</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('competitive_differentiator') }}</dd></div><div><dt class="section-kicker">Competidores</dt><dd class="mt-2 whitespace-pre-line text-sm text-slate-200">{{ $value('main_competitors') }}</dd></div></dl></article>
        </section>
    </div>
</x-app-layout>
