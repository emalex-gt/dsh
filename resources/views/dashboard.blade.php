<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="grid gap-8 lg:grid-cols-[1.25fr_0.75fr] lg:items-end">
                <div>
                    <span class="brand-badge">Area de clientes</span>
                    <h1 class="mt-5 max-w-3xl text-4xl font-semibold leading-tight text-white sm:text-5xl">
                        {{ Auth::user()->name }}, aqui tienes una vista clara de tu proyecto.
                    </h1>
                    <p class="mt-5 max-w-2xl text-sm leading-8 text-slate-300 sm:text-base">
                        Revisa la informacion principal, consulta tu brief, abre tickets y mantente alineado con nuestro equipo desde un espacio mas limpio y centralizado.
                    </p>
                </div>

                <div class="grid gap-4">
                    <div class="metric-card">
                        <p class="section-kicker">Acceso</p>
                        <p class="mt-3 text-2xl font-semibold text-white">Panel activo</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Tu espacio privado esta habilitado para seguir el proyecto y gestionar soporte.</p>
                    </div>
                    <div class="metric-card">
                        <p class="section-kicker">Proyecto</p>
                        <p class="mt-3 text-2xl font-semibold text-white">Demo Proyecto</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Tu area esta preparada para concentrar datos, entregables y soporte en un mismo flujo.</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[1.18fr_0.82fr]">
        <section class="grid gap-6">
            <div class="grid gap-6 md:grid-cols-3">
                <article class="panel-premium p-6">
                    <p class="section-kicker">01</p>
                    <h2 class="mt-4 text-2xl font-semibold text-white">Mis datos</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300">Consulta la informacion base del cliente y del proyecto desde una vista dedicada y mejor organizada.</p>
                    <a href="{{ route('client.data.show') }}" class="btn-secondary mt-6">Abrir Mis Datos</a>
                </article>

                <article class="panel-premium p-6">
                    <p class="section-kicker">02</p>
                    <h2 class="mt-4 text-2xl font-semibold text-white">Ver mi brief</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300">Accede al brief completo con objetivos, requerimientos, contexto y alcance de referencia.</p>
                    <a href="{{ route('client.brief.show') }}" class="btn-secondary mt-6">Ver brief</a>
                </article>

                <article class="panel-premium p-6">
                    <p class="section-kicker">03</p>
                    <h2 class="mt-4 text-2xl font-semibold text-white">Soporte 24/7</h2>
                    <p class="mt-4 text-sm leading-7 text-slate-300">Crea tickets, sigue respuestas del equipo y mantén cada incidencia dentro de un hilo claro.</p>
                    <a href="{{ route('client.tickets.index') }}" class="btn-primary mt-6">Abrir soporte</a>
                </article>
            </div>

            <div class="panel-premium p-6 sm:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <span class="brand-badge">Resumen</span>
                        <h2 class="mt-4 text-3xl font-semibold text-white">Todo lo esencial, a mano</h2>
                    </div>
                    <a href="{{ route('logout') }}" class="btn-secondary" onclick="event.preventDefault(); document.getElementById('logout-dashboard-form').submit();">
                        Cerrar sesion
                    </a>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="metric-card">
                        <p class="section-kicker">Brief</p>
                        <p class="mt-3 text-lg font-semibold text-white">Informacion alineada</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Consulta siempre la base del proyecto sin depender de cadenas de mensajes.</p>
                    </div>
                    <div class="metric-card">
                        <p class="section-kicker">Datos</p>
                        <p class="mt-3 text-lg font-semibold text-white">Contexto del cliente</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Revisa rapidamente contacto, razon social y datos operativos principales.</p>
                    </div>
                    <div class="metric-card">
                        <p class="section-kicker">Soporte</p>
                        <p class="mt-3 text-lg font-semibold text-white">Seguimiento ordenado</p>
                        <p class="mt-2 text-sm leading-7 text-slate-400">Tus tickets quedan centralizados con respuestas y estados visibles.</p>
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
                </div>
            </div>

            <div class="panel-premium p-6">
                <p class="section-kicker">Accesos rapidos</p>
                <div class="mt-5 grid gap-3 text-sm text-slate-300">
                    <a href="{{ route('client.data.show') }}" class="panel-soft px-5 py-4 transition hover:bg-white/10">Abrir Mis Datos</a>
                    <a href="{{ route('client.brief.show') }}" class="panel-soft px-5 py-4 transition hover:bg-white/10">Revisar brief</a>
                    <a href="{{ route('client.tickets.index') }}" class="panel-soft px-5 py-4 transition hover:bg-white/10">Entrar a soporte</a>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
