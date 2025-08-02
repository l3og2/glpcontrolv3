<section>
    <header>
        {{-- No necesitamos un título aquí porque ya está en el card-header de la vista principal --}}
    </header>

    {{-- Formulario para enviar el email de verificación (lo dejamos por si un Admin lo necesita) --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <!-- Campo Nombre -->
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @if ($errors->get('name'))
                <div class="mt-2 text-danger small">
                    @foreach ((array) $errors->get('name') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Campo Email (con lógica condicional) -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" 
                   name="email" 
                   type="email" 
                   class="form-control" 
                   value="{{ $user->email }}" 
                   required 
                   autocomplete="username"
                   {{-- La lógica clave: si el usuario NO es Admin, el campo es de solo lectura --}}
                   @unless(auth()->user()->hasRole('Admin')) readonly @endunless>
            
            {{-- Añadimos una nota para los usuarios que no son Admin --}}
            @unless(auth()->user()->hasRole('Admin'))
                <div class="form-text">
                    El correo electrónico no puede ser modificado. Por favor, contacte a un administrador para realizar este cambio.
                </div>
            @endunless

            @if ($errors->get('email'))
                <div class="mt-2 text-danger small">
                    @foreach ((array) $errors->get('email') as $message)
                        <p>{{ $message }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Lógica de verificación de email (solo relevante si el usuario es Admin y puede cambiarlo) --}}
            @if (auth()->user()->hasRole('Admin') && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="small text-muted">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 small text-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-4">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <p class="text-muted small mb-0">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>