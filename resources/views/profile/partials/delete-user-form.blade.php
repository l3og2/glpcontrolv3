<section>
    {{-- La cabecera y la descripción ya están en la vista principal 'edit.blade.php' --}}

    <!-- Botón que abre el Modal de Confirmación -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        {{ __('Delete Account') }}
    </button>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('Confirm Account Deletion') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <h6 class="h5">
                            {{ __('Are you sure you want to delete your account?') }}
                        </h6>
                        <p class="text-muted">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>
                        
                        <!-- Campo de Contraseña para Confirmación -->
                        <div class="mt-3">
                            <label for="password" class="form-label visually-hidden">{{ __('Password') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                                placeholder="{{ __('Password') }}"
                                required
                            >
                            {{-- Mostramos el error específico de la contraseña --}}
                            @if($errors->userDeletion->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->userDeletion->first('password') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            {{ __('Delete Account') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Script para abrir el modal si hay errores de validación --}}
    @if($errors->userDeletion->isNotEmpty())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmUserDeletionModal = new bootstrap.Modal(document.getElementById('confirmUserDeletionModal'));
            confirmUserDeletionModal.show();
        });
    </script>
    @endpush
    @endif

</section>