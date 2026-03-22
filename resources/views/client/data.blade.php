<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Mis Datos</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">Tu ficha principal</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Esta vista resume los datos del cliente y la informacion base del proyecto con la que estamos trabajando.
                    </p>
                    <p class="mt-3 text-sm text-slate-400">Datos base de tu cuenta y proyecto</p>
                </div>
                <a href="{{ route('client.brief.show') }}" class="btn-primary">Ver Mi Brief</a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[1.18fr_0.82fr]">
        <section class="grid gap-6">
            <article class="panel-premium p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-2xl font-semibold text-white">Datos del cliente</h2>
                    <span class="brand-badge">Cuenta activa</span>
                </div>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="metric-card"><p class="section-kicker">Nombre</p><p class="mt-3 text-xl font-semibold text-white">{{ $user->name }}</p></div>
                    <div class="metric-card"><p class="section-kicker">Correo</p><p class="mt-3 text-xl font-semibold text-white">{{ $user->email }}</p></div>
                    <div class="metric-card"><p class="section-kicker">Contacto</p><p class="mt-3 text-xl font-semibold text-white">{{ $user->contact_name ?: '-' }}</p><p class="mt-2 text-sm text-slate-400">{{ $user->contact_role ?: 'Sin cargo asignado' }}</p></div>
                    <div class="metric-card"><p class="section-kicker">Telefono</p><p class="mt-3 text-xl font-semibold text-white">{{ $user->contact_phone ?: '-' }}</p></div>
                </div>
            </article>

            <article class="panel-premium p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-white">Datos del proyecto</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="metric-card"><p class="section-kicker">Proyecto</p><p class="mt-3 text-xl font-semibold text-white">{{ $user->project_name ?: 'Demo Proyecto' }}</p></div>
                    <div class="metric-card"><p class="section-kicker">Marca</p><p class="mt-3 text-xl font-semibold text-white">{{ $user->brand_name ?: '-' }}</p></div>
                    <div class="metric-card md:col-span-2"><p class="section-kicker">Razon social</p><p class="mt-3 text-xl font-semibold text-white">{{ $user->legal_name ?: '-' }}</p></div>
                    <div class="metric-card"><p class="section-kicker">Pais</p><p class="mt-3 text-xl font-semibold text-white">{{ $user->country ?: '-' }}</p></div>
                    <div class="metric-card"><p class="section-kicker">Ciudad</p><p class="mt-3 text-xl font-semibold text-white">{{ $user->city ?: '-' }}</p></div>
                </div>
            </article>
        </section>

        <aside class="grid gap-6">
            <article class="panel-premium p-6">
                <p class="section-kicker">Atajo principal</p>
                <h2 class="mt-4 text-3xl font-semibold text-white">Brief del proyecto</h2>
                <p class="mt-4 text-sm leading-8 text-slate-300">Revisa el alcance, los objetivos y las necesidades del proyecto en una vista de consulta limpia y directa.</p>
                <a href="{{ route('client.brief.show') }}" class="btn-primary mt-6">Ver Mi Brief</a>
            </article>

            <article class="panel-premium p-6">
                <p class="section-kicker">Estado</p>
                <div class="mt-5 space-y-4">
                    <div class="stat-strip"><span class="text-sm text-slate-300">Acceso</span><span class="text-sm font-semibold text-lime-100">Activo</span></div>
                    <div class="stat-strip"><span class="text-sm text-slate-300">Proyecto</span><span class="text-sm font-semibold text-white">Demo Proyecto</span></div>
                    <div class="stat-strip"><span class="text-sm text-slate-300">Soporte</span><a href="{{ route('client.tickets.index') }}" class="text-sm font-semibold text-cyan-100">Abrir tickets</a></div>
                </div>
            </article>
        </aside>
    </div>
</x-app-layout>

