<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-g">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bienvenido a GLPControlv3 - Gas Comunal, S.A.</title>

        <!-- Carga de los assets compilados (CSS y JS) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Añadimos una librería de animaciones para un toque extra -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

        <!-- Estilos específicos para esta página de bienvenida -->
        <style>
            /* La imagen de fondo ahora tiene un efecto de paralaje y zoom sutil */
            .welcome-header {
                background: linear-gradient(rgba(20, 20, 20, 0.6), rgba(20, 20, 20, 0.6)), url('https://images.unsplash.com/photo-1621935541555-2311f2c2f73a?q=80&w=1932&auto=format&fit=crop') no-repeat center center;
                background-size: cover;
                background-attachment: fixed; /* Efecto Parallax */
                color: white;
                padding: 10rem 0;
                text-shadow: 2px 2px 5px rgba(0,0,0,0.8);
                overflow: hidden; /* Para contener el efecto de zoom */
            }
            .welcome-header-content {
                animation: fadeInDown 1s; /* Animación de entrada */
            }
            .welcome-header h1 {
                font-family: 'Montserrat', sans-serif;
                font-weight: 900;
                font-size: clamp(2.8rem, 6vw, 4.5rem);
                letter-spacing: -2px;
            }
            .welcome-header h1 .part-1 {
                color: #ff4d4d; /* Un rojo vibrante que complementa el escarlata */
            }
             .welcome-header h1 .part-2 {
                color: #f8f9fa;
            }
            .feature-icon {
                font-size: 3.5rem;
                color: #C00000; /* corporate-red */
                transition: transform 0.3s ease-in-out;
            }
            .feature-card:hover .feature-icon {
                transform: scale(1.2) rotate(5deg);
            }
            .feature-card {
                transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            }
            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
            }
            .footer {
                background-color: #343a40;
                color: #adb5bd;
            }
        </style>
    </head>
    <body class="bg-white">
        <!-- Barra de Navegación Simplificada para Invitados -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ Vite::asset('resources/images/logo_gas_comunal_transp.png') }}" alt="Logo Gas Comunal" style="height: 40px;">
                </a>
                <div class="ms-auto">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-corporate animate__animated animate__pulse animate__infinite--hover">Iniciar Sesión</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Cabecera de Bienvenida (Hero Section) -->
        <header class="welcome-header text-center">
            <div class="container welcome-header-content">
                <h1 class="display-2">
                    <span class="part-1">GLP</span><span class="part-2">Controlv3</span>
                </h1>
                <p class="lead fs-3 fw-light mt-3">La plataforma digital para la gestión y control del inventario de Gas Comunal, S.A.</p>
                <p class="mt-4">Optimizando la eficiencia, garantizando la trazabilidad.</p>
            </div>
        </header>

        <!-- Sección de Características -->
        <div class="container my-5 py-5">
            <div class="row text-center">
                <div class="col-12 mb-5">
                    <h2 class="display-5">Potencia para Nuestras Operaciones</h2>
                    <p class="lead text-muted">Una solución integral diseñada para el futuro de la distribución de GLP.</p>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm feature-card animate__animated animate__fadeInUp" data-wow-delay="0.1s">
                         <div class="card-body p-5">
                            <div class="feature-icon mb-4">
                                <i class="bi bi-speedometer2"></i> <!-- Usando iconos de Bootstrap desde la fuente -->
                            </div>
                            <h3>Eficiencia Operativa</h3>
                            <p class="text-muted">Digitalice los registros de entrada y salida para una administración más rápida y precisa, minimizando el error humano.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                     <div class="card h-100 border-0 shadow-sm feature-card animate__animated animate__fadeInUp" data-wow-delay="0.2s">
                         <div class="card-body p-5">
                            <div class="feature-icon mb-4">
                               <i class="bi bi-shield-check"></i>
                            </div>
                            <h3>Control y Seguridad</h3>
                            <p class="text-muted">Gestión de acceso por roles y números de control únicos para una trazabilidad completa de cada litro de gas.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                     <div class="card h-100 border-0 shadow-sm feature-card animate__animated animate__fadeInUp" data-wow-delay="0.3s">
                         <div class="card-body p-5">
                            <div class="feature-icon mb-4">
                               <i class="bi bi-bar-chart-line-fill"></i>
                            </div>
                            <h3>Análisis de Datos</h3>
                            <p class="text-muted">Generación de reportes y gráficos para la toma de decisiones estratégicas informadas y basadas en datos reales.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie de Página -->
        <footer class="py-4 footer">
            <div class="container text-center">
                <p>© {{ date('Y') }} Gas Comunal, S.A. Todos los derechos reservados.</p>
                <p>Desarrollado con <span class="corporate-red">♥</span> por el equipo de GLPControlv3.</p>
            </div>
        </footer>
    </body>
</html>