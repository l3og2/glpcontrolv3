@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            
            <!-- Formulario de Información del Perfil -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="m-0">{{ __('Información del Perfil') }}</h4>
                </div>
                <div class="card-body p-4">
                    <p class="card-subtitle mb-4 text-muted">
                        {{ __("Actualiza la información de perfil y la dirección de correo electrónico de tu cuenta.") }}
                    </p>
                    {{-- Este parcial contendrá el formulario para nombre y email --}}
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Formulario de Actualización de Contraseña -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="m-0">{{ __('Actualizar Contraseña') }}</h4>
                </div>
                <div class="card-body p-4">
                    <p class="card-subtitle mb-4 text-muted">
                        {{ __("Asegúrate de que tu cuenta utilice una contraseña larga y aleatoria para mantenerla segura.") }}
                    </p>
                     {{-- Este parcial contendrá el formulario para la contraseña --}}
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Formulario de Eliminación de Cuenta -->
            @role('Admin')
            <div class="card shadow-sm mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="m-0">{{ __('Eliminar Cuenta') }}</h4>
                </div>
                <div class="card-body p-4">
                    <p class="card-subtitle mb-4 text-muted">
                        {{ __('Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.') }}
                    </p>
                     {{-- Este parcial contendrá el botón para abrir el modal de confirmación --}}
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            @endrole
        </div>
    </div>
@endsection