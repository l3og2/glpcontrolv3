<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - GLPControlv3</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Estilos para centrar el formulario en la página */
        body, html { height: 100%; }
        body { display: flex; align-items: center; padding-top: 40px; padding-bottom: 40px; background-color: #f5f5f5;}
    </style>
</head>
<body class="text-center">
    <main class="w-100 m-auto" style="max-width: 400px;">
        <div class="card shadow-lg">
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <img class="mb-4" src="{{ Vite::asset('resources/images/cilindrogas_transp.png') }}" alt="Logo Gas Comunal" style="height: 40px;">
                    
                    <h1 class="h3 mb-3 fw-normal">Acceso al Sistema</h1>
                    <p class="text-muted mb-4"><span class="corporate-red">GLPControlv3</span></p>

                    
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="floatingInput" placeholder="usuario@gascomunal.com.ve" required>
                        <label for="floatingInput">Correo Electrónico</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Contraseña" required>
                        <label for="floatingPassword">Contraseña</label>
                    </div>
                    
                    
                    <button class="w-100 btn btn-lg btn-corporate" type="submit">Iniciar Sesión</button>
                    
                    <p class="mt-5 mb-3 text-muted">© {{ date('Y') }} Gas Comunal, S.A.</p>
                </form>
            </div>
        </div>
    </main>
</body>
</html>