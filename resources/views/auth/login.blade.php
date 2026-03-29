<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-3">
            <span class="brand-badge">Ingreso privado</span>
            <h2 class="text-3xl font-semibold text-white sm:text-4xl">Bienvenido a tu area de cliente</h2>
            <p class="text-sm leading-7 text-slate-300">
                Accede para revisar el estado de tu proyecto, consultar entregables y mantener la comunicacion con nuestro equipo.
            </p>
        </div>

        <div class="rounded-[28px] border border-cyan-300/20 bg-cyan-300/10 p-5">
            <p class="text-xs uppercase tracking-[0.24em] text-cyan-200">Primera vez aqui</p>
            <p class="mt-3 text-sm leading-7 text-slate-100">
                Si todavia no tienes acceso, primero debes completar el brief para que nuestro equipo revise tu proyecto y pueda crear tu cuenta de cliente.
            </p>
            <a href="{{ route('brief.edit') }}" class="btn-secondary mt-4 inline-flex">
                Completar brief
            </a>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="field-label">Correo electronico</label>
                <input id="email" class="field-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Ingresa tu correo electronico" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between gap-4">
                    <label for="password" class="field-label mb-0">Contrasena</label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-cyan-200 transition hover:text-cyan-100" href="{{ route('password.request') }}">
                            Recuperar acceso
                        </a>
                    @endif
                </div>
                <input id="password" class="field-input" type="password" name="password" required autocomplete="current-password" placeholder="Ingresa tu contrasena" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label for="remember_me" class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-slate-950 text-lime-300 focus:ring-lime-300/40" name="remember">
                Mantener sesion iniciada
            </label>

            <button type="submit" class="btn-primary w-full">
                Ingresar a mi panel
            </button>

            <div class="grid gap-3 pt-2 sm:grid-cols-3">
                <div class="panel-soft p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Proyecto</p>
                    <p class="mt-2 text-sm text-white">Estado y avances</p>
                </div>
                <div class="panel-soft p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Archivos</p>
                    <p class="mt-2 text-sm text-white">Documentos y entregables</p>
                </div>
                <div class="panel-soft p-4">
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Contacto</p>
                    <p class="mt-2 text-sm text-white">Mensajes y soporte</p>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>
