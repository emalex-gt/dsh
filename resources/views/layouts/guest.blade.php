<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DSH Studio') }} | {{ $briefMode ? 'Brief' : 'Acceso Clientes' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        @if ($briefMode)
            <div class="relative isolate min-h-screen overflow-hidden px-4 py-4 sm:px-6 lg:px-8">
                <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(39,215,198,0.18),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(176,255,76,0.1),_transparent_22%)]"></div>
                <div class="absolute left-10 top-24 -z-10 h-48 w-48 rounded-full bg-cyan-400/15 blur-3xl"></div>
                <div class="absolute bottom-16 right-8 -z-10 h-64 w-64 rounded-full bg-lime-300/10 blur-3xl"></div>

                <div class="mx-auto flex min-h-[calc(100vh-2rem)] max-w-7xl flex-col justify-center">
                    <div class="mb-6 flex justify-center lg:mb-8">
                        <img src="https://tech.dshcompany.com/wp-content/uploads/2023/11/tech1.png" alt="DSH Tech" class="h-14 w-auto opacity-95 sm:h-16" />
                    </div>

                    {{ $slot }}
                </div>
            </div>
        @else
            <div class="relative isolate min-h-screen overflow-hidden px-4 py-6 sm:px-6 lg:px-8">
                <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(39,215,198,0.18),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(176,255,76,0.1),_transparent_22%)]"></div>
                <div class="absolute left-10 top-24 -z-10 h-48 w-48 rounded-full bg-cyan-400/15 blur-3xl"></div>
                <div class="absolute bottom-16 right-8 -z-10 h-64 w-64 rounded-full bg-lime-300/10 blur-3xl"></div>

                <div class="mx-auto grid min-h-[calc(100vh-3rem)] max-w-7xl items-center gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                    <section class="hidden lg:flex lg:flex-col lg:justify-between">
                        <div class="space-y-8">
                            <a href="{{ route('login') }}" class="inline-flex">
                                <img src="https://tech.dshcompany.com/wp-content/uploads/2023/11/tech1.png" alt="DSH Tech" class="h-14 w-auto" />
                            </a>

                            <div class="space-y-5">
                                <span class="brand-badge">Plataforma privada</span>
                                <h1 class="max-w-xl text-5xl font-semibold leading-tight text-white">
                                    Todo lo importante de tu proyecto, en un solo panel.
                                </h1>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-3">
                                <div class="panel-soft p-5">
                                    <p class="text-xs uppercase tracking-[0.28em] text-cyan-200">Informacion</p>
                                    <p class="mt-3 text-sm text-slate-300">Consulta los datos clave y el contexto compartido de tu proyecto.</p>
                                </div>
                                <div class="panel-soft p-5">
                                    <p class="text-xs uppercase tracking-[0.28em] text-cyan-200">Avances</p>
                                    <p class="mt-3 text-sm text-slate-300">Sigue el progreso, revisa novedades y mantente al dia.</p>
                                </div>
                                <div class="panel-soft p-5">
                                    <p class="text-xs uppercase tracking-[0.28em] text-cyan-200">Contacto</p>
                                    <p class="mt-3 text-sm text-slate-300">Centraliza mensajes, solicitudes y observaciones del proyecto.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-2">
                            <div class="panel-dark p-5">
                                <p class="text-sm text-slate-400">Tu proyecto</p>
                                <p class="mt-2 text-2xl font-semibold text-white">Accede a la informacion que necesitas cuando la necesitas</p>
                            </div>
                            <div class="panel-dark p-5">
                                <p class="text-sm text-slate-400">Consultas</p>
                                <p class="mt-2 text-2xl font-semibold text-white">Comparte dudas, comentarios y solicitudes desde tu espacio privado</p>
                            </div>
                        </div>
                    </section>

                    <section class="w-full">
                        <div class="panel-dark mx-auto w-full max-w-xl p-6 sm:p-8">
                            <div class="mb-8 lg:hidden">
                                <a href="{{ route('login') }}" class="inline-flex">
                                    <x-application-logo />
                                </a>
                            </div>
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </div>
        @endif

        @include('layouts.partials.session-refresh-script')
    </body>
</html>
