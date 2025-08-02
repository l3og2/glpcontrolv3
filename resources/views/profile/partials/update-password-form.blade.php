<section>
    {{-- La cabecera y la descripción ya están en la vista principal 'edit.blade.php', por lo que no las necesitamos aquí. --}}

    {{-- Mostramos cualquier error de validación específico de la actualización de contraseña --}}
    @if ($errors->updatePassword->any())
        <div class="alert alert-danger mb-4" role="alert">
            <ul class="mb-0">
                @foreach ($errors->updatePassword->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <!-- Campo Contraseña Actual -->
        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
        </div>

        <!-- Campo Nueva Contraseña -->
        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
        </div>

        <!-- Campo Confirmar Nueva Contraseña -->
        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
        </div>

        <div class="d-flex align-items-center gap-4 mt-4">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            {{-- Mensaje de éxito que se desvanece --}}
            @if (session('status') === 'password-updated')
                <p class="text-success mb-0">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>