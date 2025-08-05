@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('header-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-corporate">
        <i class="bi bi-plus-circle-fill me-1"></i> Crear Nuevo Usuario
    </a>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3>Listado de Usuarios</h3>
    </div>
    <div class="card-body">
        
        {{-- ==================== INICIO DEL FORMULARIO DE FILTRO ==================== --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 border p-3 rounded bg-light">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Nombre o email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="state_id" class="form-label">Estado</label>
                    <select id="state_id" name="state_id" class="form-select">
                        <option value="">-- Todos los Estados --</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">Rol</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">-- Todos los Roles --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel-fill me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </form>
        {{-- ===================== FIN DEL FORMULARIO DE FILTRO ====================== --}}

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Estado Asignado</th>
                        <th class="text-center">Roles</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->state->name ?? 'N/A' }}</td>
                            <td class="text-center">
                                @foreach ($user->getRoleNames() as $roleName)
                                    <span class="badge bg-secondary">{{ $roleName }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">Editar</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar a este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No se encontraron usuarios que coincidan con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if ($users->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{-- Los links de paginación ya mantendrán los filtros gracias a withQueryString() en el controlador --}}
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection