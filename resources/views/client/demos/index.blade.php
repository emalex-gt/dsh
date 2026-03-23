<x-app-layout>
    <x-slot name="header">
        <div class="panel-premium surface-grid overflow-hidden px-6 py-8 sm:px-8 lg:px-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="brand-badge">Demo Proyecto</span>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-5xl">Demos y referencias</h1>
                    <p class="mt-5 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base">
                        Aqui veras las demos y mockups que el equipo haya vinculado a tu cuenta, ya sea como demo directa o como demos asignadas por categoria.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn-secondary">Volver al panel</a>
            </div>
        </div>
    </x-slot>

    @if (! $directDemo && ! $recommendedDemo && $categorizedDemos->isEmpty())
        <div class="panel-premium p-10 text-center">
            <h2 class="text-3xl font-semibold text-white">Todavia no tienes demos asignadas</h2>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-8 text-slate-300">Cuando el equipo vincule demos, referencias o mockups a tu cuenta, apareceran aqui organizadas para que las revises facilmente.</p>
        </div>
    @else
        <div class="grid gap-6">
            @if ($directDemo)
                <section class="panel-premium surface-grid overflow-hidden p-6 sm:p-8">
                    <div class="grid gap-8 lg:grid-cols-[1fr_0.98fr] lg:items-center">
                        <div>
                            <span class="brand-badge">Demo directa</span>
                            <h2 class="mt-5 text-4xl font-semibold text-white">{{ $directDemo->direct_demo_name ?: 'Vista personalizada' }}</h2>
                            <p class="mt-4 text-sm leading-8 text-slate-300">Mockups cargados directamente en la ficha del cliente.</p>
                            @if ($directDemo->direct_demo_link)
                                <p class="mt-4 break-all text-sm leading-7 text-slate-400">{{ $directDemo->direct_demo_link }}</p>
                                <div class="mt-6 flex flex-wrap gap-3">
                                    <a href="{{ $directDemo->direct_demo_link }}" target="_blank" rel="noopener noreferrer" class="btn-primary">
                                        Abrir demo directa
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="demo-preview-cluster">
                            @if ($directDemo->direct_demo_desktop_image_url)
                                <div class="demo-preview-shell">
                                    <div class="demo-preview-bar">
                                        <div class="demo-preview-dots">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                        <p class="truncate text-xs text-slate-400">Mockup escritorio</p>
                                    </div>
                                    <div class="demo-preview-frame-desktop">
                                        <img src="{{ $directDemo->direct_demo_desktop_image_url }}" alt="Mockup escritorio de demo directa">
                                    </div>
                                </div>
                            @endif

                            @if ($directDemo->direct_demo_mobile_image_url)
                                <div class="demo-preview-shell-mobile">
                                    <div class="demo-preview-bar">
                                        <div class="demo-preview-dots">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                        <p class="truncate text-xs text-slate-400">Mockup movil</p>
                                    </div>
                                    <div class="demo-preview-frame-mobile">
                                        <img src="{{ $directDemo->direct_demo_mobile_image_url }}" alt="Mockup movil de demo directa">
                                    </div>
                                </div>
                            @endif

                            @if (! $directDemo->direct_demo_desktop_image_url && ! $directDemo->direct_demo_mobile_image_url)
                                <div class="demo-preview-shell sm:col-span-2">
                                    <div class="demo-preview-frame-desktop">
                                        <div class="demo-preview-placeholder">
                                            Esta demo directa no tiene mockups cargados todavia.
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            @if ($recommendedDemo)
                <section class="panel-premium surface-grid overflow-hidden p-6 sm:p-8">
                    <div class="grid gap-8 lg:grid-cols-[1fr_0.98fr] lg:items-center">
                        <div>
                            <span class="brand-badge">Recomendada</span>
                            <h2 class="mt-5 text-4xl font-semibold text-white">{{ $recommendedDemo->name }}</h2>
                            <p class="mt-4 text-sm leading-8 text-slate-300">{{ $recommendedDemo->category?->name ?: 'Sin categoria' }}</p>
                            <p class="mt-4 break-all text-sm leading-7 text-slate-400">{{ $recommendedDemo->link }}</p>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ $recommendedDemo->link }}" target="_blank" rel="noopener noreferrer" class="btn-primary">
                                    Abrir demo recomendada
                                </a>
                            </div>
                        </div>

                        <div class="demo-preview-cluster">
                            @if ($recommendedDemo->preview_image_desktop_url)
                                <div class="demo-preview-shell">
                                    <div class="demo-preview-bar">
                                        <div class="demo-preview-dots">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                        <p class="truncate text-xs text-slate-400">Mockup escritorio</p>
                                    </div>
                                    <div class="demo-preview-frame-desktop">
                                        <img src="{{ $recommendedDemo->preview_image_desktop_url }}" alt="Preview escritorio de {{ $recommendedDemo->name }}">
                                    </div>
                                </div>
                            @endif

                            @if ($recommendedDemo->preview_image_mobile_url)
                                <div class="demo-preview-shell-mobile">
                                    <div class="demo-preview-bar">
                                        <div class="demo-preview-dots">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                        <p class="truncate text-xs text-slate-400">Mockup movil</p>
                                    </div>
                                    <div class="demo-preview-frame-mobile">
                                        <img src="{{ $recommendedDemo->preview_image_mobile_url }}" alt="Preview movil de {{ $recommendedDemo->name }}">
                                    </div>
                                </div>
                            @endif

                            @if (! $recommendedDemo->preview_image_desktop_url && ! $recommendedDemo->preview_image_mobile_url)
                                <div class="demo-preview-shell sm:col-span-2">
                                    <div class="demo-preview-frame-desktop">
                                        <div class="demo-preview-placeholder">
                                            Sube imagen a mockup escritorio y/o imagen a mockup movil desde admin para mostrar la vista previa.
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            @foreach ($categorizedDemos as $categoryName => $demos)
                <section class="panel-premium p-6 sm:p-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="section-kicker">Categoria</p>
                            <h2 class="mt-3 text-3xl font-semibold text-white">{{ $categoryName }}</h2>
                        </div>
                        <span class="brand-badge">{{ $demos->count() }} demos</span>
                    </div>

                    <div class="mt-8 grid gap-6 xl:grid-cols-2">
                        @foreach ($demos as $demo)
                            <article class="metric-card p-4 sm:p-5">
                                <div class="demo-preview-cluster">
                                    @if ($demo->preview_image_desktop_url)
                                        <div class="demo-preview-shell">
                                            <div class="demo-preview-bar">
                                                <div class="demo-preview-dots">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                                <p class="truncate text-xs text-slate-400">Mockup escritorio</p>
                                            </div>
                                            <div class="demo-preview-frame-desktop">
                                                <img src="{{ $demo->preview_image_desktop_url }}" alt="Preview escritorio de {{ $demo->name }}">
                                            </div>
                                        </div>
                                    @endif

                                    @if ($demo->preview_image_mobile_url)
                                        <div class="demo-preview-shell-mobile">
                                            <div class="demo-preview-bar">
                                                <div class="demo-preview-dots">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                                <p class="truncate text-xs text-slate-400">Mockup movil</p>
                                            </div>
                                            <div class="demo-preview-frame-mobile">
                                                <img src="{{ $demo->preview_image_mobile_url }}" alt="Preview movil de {{ $demo->name }}">
                                            </div>
                                        </div>
                                    @endif

                                    @if (! $demo->preview_image_desktop_url && ! $demo->preview_image_mobile_url)
                                        <div class="demo-preview-shell sm:col-span-2">
                                            <div class="demo-preview-frame-desktop">
                                                <div class="demo-preview-placeholder">
                                                    Sube imagen a mockup escritorio y/o imagen a mockup movil desde admin para mostrar la vista previa.
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-5">
                                    <p class="section-kicker">Demo</p>
                                    <h3 class="mt-3 text-2xl font-semibold text-white">{{ $demo->name }}</h3>
                                    <p class="mt-3 break-all text-sm leading-7 text-slate-400">{{ $demo->link }}</p>
                                    <a href="{{ $demo->link }}" target="_blank" rel="noopener noreferrer" class="btn-primary mt-6 inline-flex">
                                        Abrir demo
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</x-app-layout>
