<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'GLPControlv3') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <!-- Estilos y Scripts compilados por Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div id="app" class="d-flex align-items-center justify-content-center min-vh-100">
        
        <main class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    
                    <!-- Logo de la Empresa -->
                    <div class="text-center mb-4">
                        <a href="/">
                            <img src="{{ Vite::asset('resources/images/logo-gas-comunal.svg') }}" alt="Logo Gas Comunal" style="width: 250px; max-width: 80%;">
                        </a>
                    </div>
                    
                    <!-- Contenido del formulario (login, etc.) -->
                    <div class="card shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            @yield('content')
                        </div>
                    </div>

                </div>
            </div>
        </main>

    </div>
</body>
</html>