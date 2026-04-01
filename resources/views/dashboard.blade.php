<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-end">
                <div>
                    <span class="brand-badge">Area de clientes</span>
                    <h1 class="mt-5 max-w-3xl text-4xl font-semibold leading-tight text-white sm:text-5xl">
                        {{ Auth::user()->name }}, aqui tienes el centro operativo de tu proyecto.
                    </h1>
                    <p class="mt-5 max-w-2xl text-sm leading-8 text-slate-300 sm:text-base">
                        Accede rapido a brief, demos, facturacion, desarrollo y soporte desde una vista mejor resuelta y mas clara para el dia a dia.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                    <div class="metric-card">
                        <p class="section-kicker">Acceso</p>
                        <p class="mt-3 text-2xl font-semibold text-white">Panel activo</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Tu espacio privado esta listo para revisar entregables, accesos y seguimiento.</p>
                    </div>
                    <div class="metric-card">
                        <p class="section-kicker">Proyecto</p>
                        <p class="mt-3 text-2xl font-semibold text-white">Demo Proyecto</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Todo lo relevante queda concentrado en un flujo unificado para tu cuenta.</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
        <section class="grid gap-6">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <article class="panel-premium p-6 sm:p-7">
                    <p class="section-kicker">Datos base</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Mis Datos</h2>
                    <p class="mt-4 text-sm leading-8 text-slate-300">Consulta la informacion principal del cliente, contacto y proyecto sin entrar a menus secundarios.</p>
                    <a href="{{ route('client.data.show') }}" class="btn-secondary mt-6 inline-flex">Abrir Mis Datos</a>
                </article>

                <article class="panel-premium p-6 sm:p-7">
                    <p class="section-kicker">Proyecto</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Ver mi brief</h2>
                    <p class="mt-4 text-sm leading-8 text-slate-300">Revisa objetivos, requerimientos, contexto y alcance desde la version consolidada del brief.</p>
                    <a href="{{ route('client.brief.show') }}" class="btn-secondary mt-6 inline-flex">Ver brief</a>
                </article>

                <article class="panel-premium p-6 sm:p-7">
                    <p class="section-kicker">Referencias</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Demo Proyecto</h2>
                    <p class="mt-4 text-sm leading-8 text-slate-300">Accede a demos y referencias asignadas para revisar materiales compartidos por el equipo.</p>
                    <a href="{{ route('client.demos.index') }}" class="btn-secondary mt-6 inline-flex">Abrir demos</a>
                </article>

                <article class="panel-premium p-6 sm:p-7">
                    <p class="section-kicker">Cobros</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Facturacion</h2>
                    <p class="mt-4 text-sm leading-8 text-slate-300">Consulta presupuestos y facturas, revisa PDFs y responde aprobaciones desde una sola area.</p>
                    <a href="{{ route('client.budgets.index') }}" class="btn-secondary mt-6 inline-flex">Abrir facturacion</a>
                </article>

                <article class="panel-premium p-6 sm:p-7">
                    <p class="section-kicker">Tecnico</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Desarrollo</h2>
                    <p class="mt-4 text-sm leading-8 text-slate-300">Consulta hosting, dominio, correos y el flujo de solicitudes en `Edit Area` y `Ready Area`.</p>
                    <a href="{{ route('client.development.show') }}" class="btn-secondary mt-6 inline-flex">Abrir desarrollo</a>
                </article>

                <article class="panel-premium p-6 sm:p-7">
                    <p class="section-kicker">Comunicacion</p>
                    <h2 class="mt-4 text-3xl font-semibold text-white">Soporte 24/7</h2>
                    <p class="mt-4 text-sm leading-8 text-slate-300">Gestiona incidencias generales y mantien cada conversacion en un hilo claro con el equipo.</p>
                    <a href="{{ route('client.tickets.index') }}" class="btn-primary mt-6 inline-flex">Abrir soporte</a>
                </article>
            </div>

            <div class="panel-premium p-6 sm:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <span class="brand-badge">Resumen operativo</span>
                        <h2 class="mt-4 text-3xl font-semibold text-white">Accesos esenciales del proyecto</h2>
                    </div>
                    <a href="{{ route('logout') }}" class="btn-secondary" onclick="event.preventDefault(); document.getElementById('logout-dashboard-form').submit();">
                        Cerrar sesion
                    </a>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="metric-card">
                        <p class="section-kicker">Brief</p>
                        <p class="mt-3 text-xl font-semibold text-white">Base del proyecto</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Consulta la informacion de referencia antes de pedir cambios o revisar entregables.</p>
                    </div>
                    <div class="metric-card">
                        <p class="section-kicker">Facturacion</p>
                        <p class="mt-3 text-xl font-semibold text-white">Control economico</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Revisa documentos, aprobaciones y el estado financiero activo de tu cuenta.</p>
                    </div>
                    <div class="metric-card">
                        <p class="section-kicker">Desarrollo</p>
                        <p class="mt-3 text-xl font-semibold text-white">Accesos y cambios</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Consulta renovaciones y sigue el progreso de las solicitudes del proyecto.</p>
                    </div>
                    <div class="metric-card">
                        <p class="section-kicker">Soporte</p>
                        <p class="mt-3 text-xl font-semibold text-white">Atencion continua</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Centraliza incidencias generales, consultas y respuestas del equipo.</p>
                    </div>
                </div>
            </div>

            <form id="logout-dashboard-form" method="POST" action="{{ route('logout') }}">
                @csrf
            </form>
        </section>

        <aside class="grid gap-6">
            <div class="panel-premium p-6">
                <p class="section-kicker">Tu cuenta</p>
                <div class="mt-6 space-y-4">
                    <div class="stat-strip">
                        <span class="text-sm text-slate-300">Acceso</span>
                        <span class="text-sm font-semibold text-lime-100">Activo</span>
                    </div>
                    <div class="stat-strip">
                        <span class="text-sm text-slate-300">Correo</span>
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="stat-strip">
                        <span class="text-sm text-slate-300">Cliente</span>
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="stat-strip">
                        <span class="text-sm text-slate-300">Proyecto</span>
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->project_name ?: 'Demo Proyecto' }}</span>
                    </div>
                </div>
            </div>

            <div class="panel-premium p-6">
                <p class="section-kicker">Accesos rapidos</p>
                <div class="mt-5 grid gap-3 text-sm text-slate-300">
                    <a href="{{ route('client.data.show') }}" class="panel-soft px-5 py-4 transition hover:bg-white/10">Abrir Mis Datos</a>
                    <a href="{{ route('client.brief.show') }}" class="panel-soft px-5 py-4 transition hover:bg-white/10">Revisar brief</a>
                    <a href="{{ route('client.demos.index') }}" class="panel-soft px-5 py-4 transition hover:bg-white/10">Abrir Demo Proyecto</a>
                    <a href="{{ route('client.budgets.index') }}" class="panel-soft px-5 py-4 transition hover:bg-white/10">Entrar a facturacion</a>
                    <a href="{{ route('client.development.show') }}" class="panel-soft px-5 py-4 transition hover:bg-white/10">Entrar a desarrollo</a>
                    <a href="{{ route('client.tickets.index') }}" class="panel-soft px-5 py-4 transition hover:bg-white/10">Entrar a soporte</a>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
