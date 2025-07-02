@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3>Crear Nuevo Usuario</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nombre Completo</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="state_id" class="form-label">Estado de Asignación</label>
                <select name="state_id" class="form-select">
                    <option value="">Ninguno (Para Admins)</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Roles</label>
                @foreach ($roles as $role)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}">
                        <label class="form-check-label">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-corporate">Guardar Usuario</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
