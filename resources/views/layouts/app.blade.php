<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GLPControlv3') }}</title>

    <!-- Bootstrap 5 (desde Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Logo opcional -->
    @csrf
</head>
<body>
    <div class="bg-light min-vh-100 d-flex flex-column">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow-sm py-3 mb-4">
                <div class="container">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-grow-1">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>

