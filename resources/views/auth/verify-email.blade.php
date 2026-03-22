<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-3">
            <span class="brand-badge">Verificacion</span>
            <h2 class="text-3xl font-semibold text-white">Confirma tu correo electronico</h2>
            <p class="text-sm leading-7 text-slate-300">
                Antes de continuar, revisa tu bandeja y haz clic en el enlace que te enviamos. Si no lo recibiste, podemos reenviarlo.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="rounded-2xl border border-lime-300/20 bg-lime-300/10 px-4 py-3 text-sm font-medium text-lime-100">
                Te enviamos un nuevo enlace de verificacion al correo registrado.
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row">
            <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
                @csrf
                <button type="submit" class="btn-primary w-full">
                    Reenviar verificacion
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit" class="btn-secondary w-full">
                    Cerrar sesion
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
