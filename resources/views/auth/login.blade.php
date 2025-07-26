<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - GLPControlv3</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body, html {
            height: 100%;
            background-color: #f8f9fa;
        }
        .login-container {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
        }
    </style>
</head>
<body class="login-container">
    <main class="w-100 m-auto" style="max-width: 400px;">
        <div class="card shadow">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="text-center mb-4">
                        <img class="mb-3" src="{{ Vite::asset('resources/images/cilindrogas_transp.png') }}" alt="Logo" style="height: 60px;">
                        <h1 class="h3 mb-3">Acceso al Sistema</h1>
                        <p class="text-muted"><span class="text-danger fw-bold">GLPControlv3</span></p>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control form-control-lg" id="email" placeholder="usuario@gascomunal.com.ve" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="Contraseña" required>
                    </div>
                    
                    <button class="w-100 btn btn-primary btn-lg" type="submit">Iniciar Sesión</button>
                    
                    <p class="mt-4 mb-0 text-center text-muted small">© {{ date('Y') }} Gas Comunal, S.A.</p>
                </form>
            </div>
        </div>
    </main>
</body>
</html>