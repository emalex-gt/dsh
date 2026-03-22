<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-3">
            <span class="brand-badge">Nueva contrasena</span>
            <h2 class="text-3xl font-semibold text-white">Configura un nuevo acceso</h2>
            <p class="text-sm leading-7 text-slate-300">
                Define una contrasena segura para volver a ingresar al area privada.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="field-label">Correo electronico</label>
                <input id="email" class="field-input" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="password" class="field-label">Nueva contrasena</label>
                <input id="password" class="field-input" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="password_confirmation" class="field-label">Confirmar contrasena</label>
                <input id="password_confirmation" class="field-input" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="btn-primary w-full">
                Guardar nueva contrasena
            </button>
        </form>
    </div>
</x-guest-layout>
