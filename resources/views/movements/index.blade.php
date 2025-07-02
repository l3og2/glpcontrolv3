@extends('layouts.app')

@section('title', 'Movimientos de Inventario')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Listado de Movimientos de Inventario</h3>
        <a href="{{ route('movements.create') }}" class="btn btn-corporate">
            
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill me-1" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
            </svg>
            Registrar Nuevo Movimiento
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>N° Controlt</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Producto/Tanque</th>
                        <th>Volumen (Lts)</th>
                        <th>Registrado Por</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                    <tr>
                        <td><strong>{{ $movement->control_number }}</strong></td>
                        <td>{{ $movement->movement_date->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($movement->type == 'entrada')
                                <span class="badge bg-success">Entrada</span>
                            @else
                                <span class="badge bg-danger">Salida</span>
                            @endif
                        </td>
                        <td>{{ $movement->product->name ?? $movement->tank->name_location ?? 'N/A' }}</td>
                        <td>{{ number_format($movement->volume_liters, 2, ',', '.') }}</td>
                        <td>{{ $movement->user->name }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">{{ ucfirst($movement->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No se han registrado movimientos todavía.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        
        <div class="d-flex justify-content-end">
            {{ $movements->links() }}
        </div>
    </div>
</div>
@endsection