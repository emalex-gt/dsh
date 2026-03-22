<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-3">
            <span class="brand-badge">Seguridad</span>
            <h2 class="text-3xl font-semibold text-white">Confirma tu contrasena</h2>
            <p class="text-sm leading-7 text-slate-300">
                Esta es una zona sensible de la plataforma. Ingresa tu contrasena para continuar.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <label for="password" class="field-label">Contrasena</label>
                <input id="password" class="field-input" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <button type="submit" class="btn-primary w-full">
                Confirmar acceso
            </button>
        </form>
    </div>
</x-guest-layout>
