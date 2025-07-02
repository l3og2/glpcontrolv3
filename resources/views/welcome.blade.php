<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bienvenido a GLPControlv3 - Gas Comunal, S.A.</title>

        <!-- Carga de los assets compilados (CSS y JS) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Estilos específicos para esta página de bienvenida -->
        <style>
            .welcome-header {
                /* Si tienes una imagen corporativa, reemplaza la URL de via.placeholder.com */
                /* La imagen debe estar en resources/images/ y referenciarla con Vite::asset() */
                background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1594950438832-7a7d4f131a1e?q=80&w=2070&auto=format&fit=crop') no-repeat center center;
                background-size: cover;
                color: white;
                padding: 8rem 0;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
            }
            .welcome-header h1 {
                font-family: 'Montserrat', sans-serif;
                font-weight: 900;
                font-size: clamp(2.5rem, 5vw, 4rem); /* Tamaño de fuente adaptable */
            } 
            .feature-icon {
                font-size: 3rem;
                color: #C00000; /* corporate-red */
            }
        </style>
    </head>
    <body>
        <!-- Barra de Navegación Simplificada para Invitados -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ Vite::asset('resources/images/logo_gas_comunal_transp.png') }}" alt="Logo Gas Comunal" style="height: 40px;">
                </a>
                <div class="ms-auto">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-corporate">Iniciar Sesión</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Cabecera de Bienvenida (Hero Section) -->
        <header class="welcome-header text-center">
            <div class="container">
                <h1 class="display-3">GLPControlv3</h1>
                <p class="lead fs-4">Sistema de Gestión de Inventario para la distribución de Gas Licuado de Petróleo.</p>
                <p>Una herramienta para optimizar la eficiencia y el control de nuestras operaciones.</p>
            </div>
        </header>

        <!-- Sección de Características -->
        <div class="container my-5 py-5">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <!-- Icono de Bootstrap para "Eficiencia" -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-speedometer2" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4zM3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10zm9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5zm.754-4.246a.389.389 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.389.389 0 0 0-.029-.518z"/><path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A7.988 7.988 0 0 1 0 10zm8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3z"/></svg>
                    </div>
                    <h3>Eficiencia Operativa</h3>
                    <p class="lead text-muted">Digitalice los registros de entrada y salida para una administración más rápida y precisa.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <!-- Icono de Bootstrap para "Control" -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-shield-check" viewBox="0 0 16 16"><path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.06.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.95-1.095-2.287-1.827-4.262-2.308C9.377.5-6.84 0 8 0c-1.84 0-3.585.51-4.662 1.59z"/><path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/></svg>
                    </div>
                    <h3>Control y Seguridad</h3>
                    <p class="lead text-muted">Gestión de acceso por roles y números de control únicos para una trazabilidad completa.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <!-- Icono de Bootstrap para "Datos" -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-bar-chart-line-fill" viewBox="0 0 16 16"><path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1V2z"/></svg>
                    </div>
                    <h3>Análisis de Datos</h3>
                    <p class="lead text-muted">Generación de reportes y gráficos para la toma de decisiones estratégicas informadas.</p>
                </div>
            </div>
        </div>

        <!-- Pie de Página -->
        <footer class="py-4 mt-5 border-top">
            <div class="container text-center">
                <p class="text-muted">&copy; {{ date('Y') }} Gas Comunal, S.A. Todos los derechos reservados.</p>
            </div>
        </footer>
    </body>
</html>

