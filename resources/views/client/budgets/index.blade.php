<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Facturacion</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">Facturacion del proyecto</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Revisa presupuestos pendientes y consulta las facturas emitidas para tu cuenta desde un unico espacio.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn-secondary">Volver al panel</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="rounded-[24px] border border-lime-300/20 bg-lime-300/10 px-4 py-3 text-sm font-medium text-lime-100">{{ session('status') }}</div>
    @endif

    <section class="grid gap-6">
        <article class="panel-premium p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="section-kicker">Presupuestos</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Pendientes de respuesta</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-8 text-slate-300">
                        Cuando apruebes un presupuesto dejara de mostrarse aqui y el seguimiento economico continuara desde las facturas emitidas.
                    </p>
                </div>
                <div class="rounded-[24px] border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    {{ $budgets->count() }} {{ \Illuminate\Support\Str::plural('pendiente', $budgets->count()) }}
                </div>
            </div>

            @if ($budgets->isEmpty())
                <div class="mt-8 rounded-[28px] border border-dashed border-white/10 bg-white/5 px-6 py-10 text-center">
                    <h3 class="text-2xl font-semibold text-white">No tienes presupuestos pendientes</h3>
                    <p class="mx-auto mt-4 max-w-2xl text-sm leading-8 text-slate-300">
                        Cuando el equipo publique un nuevo presupuesto o actualice uno con cambios, aparecera aqui para su revision.
                    </p>
                </div>
            @else
                <div class="mt-8 grid gap-5">
                    @foreach ($budgets as $budget)
                        <article class="rounded-[28px] border border-white/10 bg-white/5 p-5 sm:p-6">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <span class="brand-badge">{{ \App\Models\Budget::statusOptions()[$budget->status] ?? ucfirst($budget->status) }}</span>
                                    <h3 class="mt-4 text-2xl font-semibold text-white">{{ $budget->title }}</h3>
                                    <p class="mt-3 text-sm leading-7 text-slate-400">Emitido {{ optional($budget->issued_at)->format('d/m/Y H:i') ?: '-' }}</p>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('client.budgets.show', $budget) }}" class="btn-primary">Revisar presupuesto</a>
                                    <a href="{{ route('client.budgets.file', $budget) }}" target="_blank" class="btn-secondary">Abrir PDF</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="panel-premium p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="section-kicker">Facturas</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Documentos emitidos</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-8 text-slate-300">
                        Aqui podras consultar cada factura cargada por administracion y verificar si su estado actual es pagada o no pagada.
                    </p>
                </div>
                <div class="rounded-[24px] border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    {{ $invoices->count() }} {{ \Illuminate\Support\Str::plural('factura', $invoices->count()) }}
                </div>
            </div>

            @if ($invoices->isEmpty())
                <div class="mt-8 rounded-[28px] border border-dashed border-white/10 bg-white/5 px-6 py-10 text-center">
                    <h3 class="text-2xl font-semibold text-white">Todavia no hay facturas cargadas</h3>
                    <p class="mx-auto mt-4 max-w-2xl text-sm leading-8 text-slate-300">
                        Cuando se emita la primera factura para tu proyecto la veras aqui con acceso al PDF y su estado de pago.
                    </p>
                </div>
            @else
                <div class="mt-8 grid gap-5">
                    @foreach ($invoices as $invoice)
                        <article class="rounded-[28px] border border-white/10 bg-white/5 p-5 sm:p-6">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <span class="brand-badge">{{ \App\Models\Invoice::statusOptions()[$invoice->status] ?? ucfirst($invoice->status) }}</span>
                                    <h3 class="mt-4 text-2xl font-semibold text-white">{{ $invoice->title }}</h3>
                                    <p class="mt-3 text-sm leading-7 text-slate-400">
                                        Emitida {{ optional($invoice->issued_at)->format('d/m/Y H:i') ?: '-' }}
                                        @if ($invoice->status === 'pagada' && $invoice->paid_at)
                                            <span class="mx-2 text-slate-600">|</span>Pagada {{ $invoice->paid_at->format('d/m/Y H:i') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('client.invoices.show', $invoice) }}" class="btn-primary">Ver factura</a>
                                    <a href="{{ route('client.invoices.file', $invoice) }}" target="_blank" class="btn-secondary">Abrir PDF</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </article>
    </section>
</x-app-layout>
