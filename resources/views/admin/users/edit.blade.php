@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3>Editar Usuario: {{ $user->name }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT') 
           
            @if ($errors->any())
            
            Por favor, corrige los siguientes errores:

                        @foreach ($errors->all() as $error)

                {{ $error }}

                        @endforeach

            @endif
            
                        <!-- Campo Nombre -->
            <div class="mb-3">
                <label for="name" class="form-label">Nombre Completo</label>
                <input type="text" 
                       name="name"
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $user->name) }}" 
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Campo Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" 
                       name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email', $user->email) }}" 
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Campo Contraseña -->
            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña (Opcional)</label>
                <input type="password" 
                       name="password"
                       class="form-control @error('password') is-invalid @enderror">
                <small class="form-text text-muted">Dejar en blanco para no cambiar la contraseña.</small>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Campo Estado -->
            <div class="mb-3">
                <label for="state_id" class="form-label">Estado de Asignación</label>
                <select name="state_id" class="form-select">
                    <option value="">Ninguno</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}" @if(old('state_id', $user->state_id) == $state->id) selected @endif>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Campo Roles -->
            <div class="mb-3">
                <label class="form-label">Roles</label>
                @foreach ($roles as $role)
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="roles[]" 
                               value="{{ $role->name }}"
                               @if(in_array($role->name, old('roles', $user->getRoleNames()->toArray()))) checked @endif>
                        <label class="form-check-label">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-corporate">Actualizar Usuario</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection