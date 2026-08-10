<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DSH Studio') }} | Panel de clientes</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.16),_transparent_24%),radial-gradient(circle_at_80%_10%,_rgba(163,230,53,0.11),_transparent_18%)]"></div>
            @include('layouts.navigation')

            <main class="px-4 pb-10 pt-6 sm:px-6 lg:px-8">
                @isset($header)
                    <header class="mx-auto mb-6 max-w-7xl">
                        {{ $header }}
                    </header>
                @endisset

                <div class="mx-auto max-w-7xl">
                    @auth
                        @if (count(auth()->user()->development_expired_notifications))
                            <div class="mb-6 grid gap-4">
                                @foreach (auth()->user()->development_expired_notifications as $notification)
                                    <div class="alert-critical">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                            <div>
                                                <p class="alert-critical-kicker">{{ $notification['title'] }}</p>
                                                <p class="mt-3 text-base font-medium text-white">{{ $notification['message'] }}</p>
                                            </div>
                                            <div class="flex flex-wrap gap-3">
                                                <a href="{{ $notification['facturation_url'] }}" class="btn-primary">Ir a Facturacion</a>
                                                <a href="{{ $notification['detail_url'] }}" class="btn-secondary">{{ $notification['detail_label'] }}</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endauth

                    {{ $slot }}
                </div>
            </main>
        </div>

        @include('layouts.partials.session-refresh-script')
    </body>
</html>
