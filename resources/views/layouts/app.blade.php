<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DSH Studio') }} | Panel de clientes</title>

        <!-- Scripts -->
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
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
