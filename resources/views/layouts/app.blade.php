<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

        <link rel="manifest" href="/manifest.webmanifest">
        <meta name="theme-color" content="#111827">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

       <link rel="stylesheet" href="{{ asset('css/my-notes-theme.css') }}?v={{ filemtime(public_path('css/my-notes-theme.css')) }}">

        <script>
            (function () {
                const theme = localStorage.getItem('theme') || 'light';
                const isDark = theme === 'dark';

                document.documentElement.classList.toggle('app-dark', isDark);
                document.documentElement.classList.toggle('app-light', !isDark);
                document.documentElement.dataset.theme = theme;
            })();
        </script>
    </head>

    <body class="font-sans antialiased overflow-hidden h-screen">
        <div id="appShell" class="h-screen bg-gray-100">
            @if(!request()->is('notes*'))
                @include('layouts.navigation')
            @endif

            <main id="appMain" class="h-screen p-0 m-0">
                {{ $slot }}
            </main>

            @auth
                @if(!auth()->user()->hasVerifiedEmail())
                    <div class="bg-yellow-100 text-yellow-800 px-4 py-3 text-center text-sm font-medium">
                        Tài khoản của bạn chưa được xác minh. Vui lòng kiểm tra email để kích hoạt tài khoản.
                    </div>
                @endif
            @endauth
        </div>

        <script>
            if ("serviceWorker" in navigator) {
                window.addEventListener("load", function () {
                    navigator.serviceWorker
                        .register("/sw.js")
                        .then(function () {
                            console.log("Service Worker registered");
                        })
                        .catch(function (error) {
                            console.error("Service Worker registration failed:", error);
                        });
                });
            }
        </script>

        <script src="/js/offline-notes.js"></script>
    </body>
</html>