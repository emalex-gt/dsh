<nav x-data="{ open: false }" class="px-4 pt-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="panel-premium flex items-center justify-between px-4 py-4 sm:px-6">
            <div class="flex items-center gap-4 lg:gap-8">
                <a href="{{ route('dashboard') }}" class="inline-flex shrink-0">
                    <img src="https://tech.dshcompany.com/wp-content/uploads/2023/11/tech1.png" alt="DSH Tech" class="h-12 w-auto" />
                </a>

                <div class="hidden items-center gap-3 lg:flex">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'nav-pill-active' : 'nav-pill' }}">
                        Mi panel
                    </a>
                    <a href="{{ route('client.data.show') }}" class="{{ request()->routeIs('client.data.*') ? 'nav-pill-active' : 'nav-pill' }}">
                        Mis Datos
                    </a>
                    <a href="{{ route('client.demos.index') }}" class="{{ request()->routeIs('client.demos.*') ? 'nav-pill-active' : 'nav-pill' }}">Demo Proyecto</a>
                    <a href="{{ route('client.budgets.index') }}" class="{{ request()->routeIs('client.budgets.*') ? 'nav-pill-active' : 'nav-pill' }}">Facturacion</a>
                    <span class="ghost-pill">Desarrollo</span>
                    <a href="{{ route('client.tickets.index') }}" class="{{ request()->routeIs('client.tickets.*') ? 'nav-pill-active' : 'nav-pill' }}">
                        Soporte 24/7
                    </a>
                </div>
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                @auth
                    <div class="rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2.5 text-sm text-cyan-100">
                        {{ Auth::user()->name }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-primary px-4 py-2.5">
                            Cerrar sesion
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-primary px-4 py-2.5">
                        Iniciar sesion
                    </a>
                @endauth
            </div>

            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-full border border-white/10 p-2 text-slate-300 transition hover:bg-white/5 hover:text-white">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="panel-premium mt-3 space-y-3 px-4 py-4">
            <a href="{{ route('dashboard') }}" class="block {{ request()->routeIs('dashboard') ? 'nav-pill-active text-center' : 'nav-pill text-center' }}">
                Mi panel
            </a>
            <a href="{{ route('client.data.show') }}" class="block {{ request()->routeIs('client.data.*') ? 'nav-pill-active text-center' : 'nav-pill text-center' }}">
                Mis Datos
            </a>
            <a href="{{ route('client.demos.index') }}" class="block {{ request()->routeIs('client.demos.*') ? 'nav-pill-active text-center' : 'nav-pill text-center' }}">Demo Proyecto</a>
            <a href="{{ route('client.budgets.index') }}" class="block {{ request()->routeIs('client.budgets.*') ? 'nav-pill-active text-center' : 'nav-pill text-center' }}">Facturacion</a>
            <span class="ghost-pill block text-center">Desarrollo</span>
            <a href="{{ route('client.tickets.index') }}" class="block {{ request()->routeIs('client.tickets.*') ? 'nav-pill-active text-center' : 'nav-pill text-center' }}">
                Soporte 24/7
            </a>

            @auth
                <div class="rounded-[24px] border border-cyan-300/20 bg-cyan-300/10 px-4 py-3 text-sm text-cyan-100">
                    {{ Auth::user()->name }} - {{ Auth::user()->email }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full">
                        Cerrar sesion
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-primary w-full text-center">
                    Iniciar sesion
                </a>
            @endauth
        </div>
    </div>
</nav>


