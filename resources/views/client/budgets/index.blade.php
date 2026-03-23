<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Facturacion</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">Presupuestos</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Revisa tus presupuestos, descarga el PDF y deja constancia de aprobacion desde tu panel privado.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn-secondary">Volver al panel</a>
            </div>
        </div>
    </x-slot>

    @if ($budgets->isEmpty())
        <div class="panel-premium p-10 text-center">
            <h2 class="text-3xl font-semibold text-white">Todavia no hay presupuestos disponibles</h2>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-8 text-slate-300">Cuando el equipo cargue un presupuesto para tu cuenta, aparecera aqui listo para revisar.</p>
        </div>
    @else
        <div class="grid gap-6">
            @foreach ($budgets as $budget)
                <article class="panel-premium p-6 sm:p-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <span class="brand-badge">{{ \App\Models\Budget::statusOptions()[$budget->status] ?? ucfirst($budget->status) }}</span>
                            <h2 class="mt-4 text-3xl font-semibold text-white">{{ $budget->title }}</h2>
                            <p class="mt-4 text-sm leading-7 text-slate-400">Emitido {{ optional($budget->issued_at)->format('d/m/Y H:i') ?: '-' }}</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('client.budgets.show', $budget) }}" class="btn-primary">Ver presupuesto</a>
                            <a href="{{ route('client.budgets.file', $budget) }}" target="_blank" class="btn-secondary">Abrir PDF</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-app-layout>
