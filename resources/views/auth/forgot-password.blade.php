<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-3">
            <span class="brand-badge">Recuperacion</span>
            <h2 class="text-3xl font-semibold text-white">Restablecer contrasena</h2>
            <p class="text-sm leading-7 text-slate-300">
                Indica tu correo y te enviaremos un enlace para definir una nueva contrasena.
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="field-label">Correo electronico</label>
                <input id="email" class="field-input" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="cliente@empresa.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="btn-primary flex-1">
                    Enviar enlace de recuperacion
                </button>
                <a href="{{ route('login') }}" class="btn-secondary flex-1">
                    Volver al acceso
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
