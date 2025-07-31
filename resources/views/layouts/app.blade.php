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
              
    <main class="py-4">
        <div class="container">
        
        <!-- Sección para el Título de la Página y Acciones -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0 text-gray-800">@yield('title')</h2>
                <div>
                    {{-- ESTA LÍNEA ES LA MÁS IMPORTANTE --}}
                    @yield('header-actions') 
                </div>
            </div>

        <!-- Alertas de Sesión -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

        <!-- Contenido Principal -->
            @yield('content')

            </div>
    </main>
     
    </div>

    @stack('scripts')
</body>
</html>

