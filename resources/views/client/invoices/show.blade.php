<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Facturacion</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">{{ $invoice->title }}</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Consulta el PDF de la factura y revisa su estado actual de pago dentro de tu panel privado.
                    </p>
                </div>
                <a href="{{ route('client.budgets.index') }}" class="btn-secondary">Volver a facturacion</a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6">
        <section class="panel-premium overflow-hidden p-2 sm:p-3">
            <div class="overflow-hidden rounded-[24px] border border-white/10 bg-white">
                <iframe src="{{ route('client.invoices.file', $invoice) }}" class="h-[82vh] min-h-[760px] w-full border-0 xl:h-[88vh]" title="PDF de {{ $invoice->title }}"></iframe>
            </div>
        </section>

        <aside class="grid gap-6 lg:grid-cols-2">
            <article class="panel-premium p-6">
                <p class="section-kicker">Estado</p>
                <div class="mt-5 space-y-4 text-sm">
                    <div class="stat-strip"><span class="text-slate-300">Estado actual</span><span class="font-semibold text-white">{{ \App\Models\Invoice::statusOptions()[$invoice->status] ?? ucfirst($invoice->status) }}</span></div>
                    <div class="stat-strip"><span class="text-slate-300">Emitida</span><span class="font-semibold text-white">{{ optional($invoice->issued_at)->format('d/m/Y H:i') ?: '-' }}</span></div>
                    <div class="stat-strip"><span class="text-slate-300">Pagada</span><span class="font-semibold text-white">{{ optional($invoice->paid_at)->format('d/m/Y H:i') ?: 'Pendiente' }}</span></div>
                </div>
            </article>

            <article class="panel-premium p-6">
                <p class="section-kicker">Documento</p>
                <h2 class="mt-4 text-3xl font-semibold text-white">Factura en PDF</h2>
                <p class="mt-4 text-sm leading-8 text-slate-300">
                    Puedes revisar el documento desde esta misma pantalla o abrirlo en una pestaña aparte para descargarlo y compartirlo internamente.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('client.invoices.file', $invoice) }}" target="_blank" class="btn-primary">Abrir PDF</a>
                    <a href="{{ route('client.budgets.index') }}" class="btn-secondary">Volver a facturacion</a>
                </div>
            </article>
        </aside>
    </div>
</x-app-layout>
