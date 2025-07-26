@extends('layouts.app')

@section('title', 'Perfil de Usuario')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
            <!-- Formulario de Información del Perfil -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="m-0 corporate-red">{{ __('Profile Information') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        {{ __("Update your account's profile information and email address.") }}
                    </p>
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Formulario de Actualización de Contraseña -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="m-0 corporate-red">{{ __('Update Password') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        {{ __("Ensure your account is using a long, random password to stay secure.") }}
                    </p>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Formulario de Eliminación de Cuenta -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="m-0">{{ __('Delete Account') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                    </p>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
@endsection