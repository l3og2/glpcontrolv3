@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Listado de Usuarios</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-corporate">
            Crear Nuevo Usuario
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Estado Asignado</th>
                        <th>Roles</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->state->name ?? 'N/A' }}</td>
                        <td>
                            @foreach ($user->getRoleNames() as $role)
                                <span class="badge bg-secondary me-1">{{ $role }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('¿Está seguro de que desea eliminar a este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            <form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
