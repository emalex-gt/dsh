<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Desarrollo</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">Accesos, solicitudes y entregas</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Consulta la informacion tecnica del proyecto y gestiona cambios de desarrollo sin mezclarlo con el soporte general.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn-secondary">Volver al panel</a>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6">
        <nav class="panel-premium flex flex-col gap-3 p-4 sm:flex-row sm:flex-wrap">
            <a href="{{ route('client.development.show', 'hosting') }}" class="{{ $section === 'hosting' ? 'nav-pill-active' : 'nav-pill' }} text-center">Hosting</a>
            <a href="{{ route('client.development.show', 'dominio') }}" class="{{ $section === 'dominio' ? 'nav-pill-active' : 'nav-pill' }} text-center">Dominio</a>
            <a href="{{ route('client.development.show', 'email') }}" class="{{ $section === 'email' ? 'nav-pill-active' : 'nav-pill' }} text-center">Email</a>
            <a href="{{ route('client.development.requests.index') }}" class="nav-pill text-center">Edit Area</a>
            <a href="{{ route('client.development.ready.index') }}" class="nav-pill text-center">Ready Area</a>
        </nav>

        @if ($section === 'hosting')
            <section class="panel-premium p-6 sm:p-8">
                <p class="section-kicker">Hosting</p>
                <h2 class="mt-4 text-3xl font-semibold text-white">Datos del hosting</h2>

                @if (blank($user->hosting_link) && blank($user->hosting_username) && blank($user->hosting_password) && blank($user->hosting_expires_at) && blank($user->hosting_price))
                    <p class="mt-6 rounded-[28px] border border-dashed border-white/10 bg-white/5 px-6 py-8 text-sm leading-8 text-slate-300">
                        No tiene asignado hosting.
                    </p>
                @else
                    <div class="mt-8 grid gap-4 lg:grid-cols-2">
                        <div class="metric-card">
                            <p class="section-kicker">Acceso</p>
                            <div class="mt-4 space-y-4 text-sm">
                                <div class="stat-strip"><span class="text-slate-300">Enlace</span><span class="font-semibold text-white">{{ $user->hosting_link ?: 'No definido' }}</span></div>
                                <div class="stat-strip"><span class="text-slate-300">Usuario</span><span class="font-semibold text-white">{{ $user->hosting_username ?: 'No definido' }}</span></div>
                                <div class="stat-strip"><span class="text-slate-300">Contrasena</span><span class="font-semibold text-white">{{ $user->hosting_password ?: 'No definida' }}</span></div>
                            </div>
                            @if ($user->hosting_link)
                                <a href="{{ $user->hosting_link }}" target="_blank" rel="noreferrer" class="btn-secondary mt-6 inline-flex">Abrir acceso</a>
                            @endif
                        </div>

                        <div class="metric-card">
                            <p class="section-kicker">Renovacion</p>
                            <div class="mt-4 space-y-4 text-sm">
                                <div class="stat-strip"><span class="text-slate-300">Vencimiento</span><span class="font-semibold text-white">{{ optional($user->hosting_expires_at)->format('d/m/Y') ?: 'No definido' }}</span></div>
                                <div class="stat-strip"><span class="text-slate-300">Precio</span><span class="font-semibold text-white">{{ $user->hosting_price ?: 'No definido' }}</span></div>
                            </div>
                        </div>
                    </div>
                @endif
            </section>
        @elseif ($section === 'dominio')
            <section class="panel-premium p-6 sm:p-8">
                <p class="section-kicker">Dominio</p>
                <h2 class="mt-4 text-3xl font-semibold text-white">Datos del dominio</h2>

                @if (blank($user->domain_name) && blank($user->domain_expires_at) && blank($user->domain_price))
                    <p class="mt-6 rounded-[28px] border border-dashed border-white/10 bg-white/5 px-6 py-8 text-sm leading-8 text-slate-300">
                        No tiene asignado dominio.
                    </p>
                @else
                    <div class="mt-8 grid gap-4 lg:grid-cols-2">
                        <div class="metric-card">
                            <p class="section-kicker">Dominio</p>
                            <div class="mt-4 space-y-4 text-sm">
                                <div class="stat-strip"><span class="text-slate-300">Nombre</span><span class="font-semibold text-white">{{ $user->domain_name ?: 'No definido' }}</span></div>
                                <div class="stat-strip"><span class="text-slate-300">Vencimiento</span><span class="font-semibold text-white">{{ optional($user->domain_expires_at)->format('d/m/Y') ?: 'No definido' }}</span></div>
                                <div class="stat-strip"><span class="text-slate-300">Precio</span><span class="font-semibold text-white">{{ $user->domain_price ?: 'No definido' }}</span></div>
                            </div>
                        </div>
                    </div>
                @endif
            </section>
        @else
            <section class="panel-premium p-6 sm:p-8">
                <p class="section-kicker">Email</p>
                <h2 class="mt-4 text-3xl font-semibold text-white">Cuentas de correo</h2>

                @if ($user->emailAccounts->isEmpty())
                    <p class="mt-6 rounded-[28px] border border-dashed border-white/10 bg-white/5 px-6 py-8 text-sm leading-8 text-slate-300">
                        No tiene asignado correo electronico.
                    </p>
                @else
                    <div class="mt-8 grid gap-4 xl:grid-cols-2">
                        @foreach ($user->emailAccounts as $account)
                            <article class="metric-card">
                                <p class="section-kicker">{{ $account->label ?: 'Cuenta de correo' }}</p>
                                <h3 class="mt-4 text-2xl font-semibold text-white">{{ $account->email }}</h3>
                                <div class="mt-5 space-y-4 text-sm">
                                    <div class="stat-strip"><span class="text-slate-300">Acceso</span><span class="font-semibold text-white">{{ $account->access_link ?: 'No definido' }}</span></div>
                                    <div class="stat-strip"><span class="text-slate-300">Usuario</span><span class="font-semibold text-white">{{ $account->username ?: 'No definido' }}</span></div>
                                    <div class="stat-strip"><span class="text-slate-300">Contrasena</span><span class="font-semibold text-white">{{ $account->password ?: 'No definida' }}</span></div>
                                </div>
                                @if ($account->access_link)
                                    <a href="{{ $account->access_link }}" target="_blank" rel="noreferrer" class="btn-secondary mt-6 inline-flex">Abrir webmail</a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif
    </div>
</x-app-layout>
