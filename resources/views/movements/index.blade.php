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
            Registrar Movimiento Entrada
        </a>

        <a href="{{ route('movements.create_batch_salida') }}" class="btn btn-corporate">
            
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill me-1" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
            </svg>
            Registrar Movimiento Salida
        </a>

    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>N° Control</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Producto/Tanque</th>
                        <th>Volumen (Lts)</th>
                        <th>Total Ventas</th>
                        <th>Registrado Por</th>
                        <th>Estado</th>
                    </tr>
                </thead>
               <tbody>
    @forelse ($movements as $movement)
        <tr>
            <td><strong>{{ $movement->first_control_number }}</strong><br>
                <small class="text-muted">Lote: {{ substr($movement->batch_id, 0, 8) }}</small>
            </td>
            <td>{{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y') }}</td>
            <td>
                @if($movement->type == 'entrada')
                    <span class="badge bg-success">Entrada</span>
                @else
                    <span class="badge bg-danger">Salida (Lote)</span>
                @endif
            </td>
            <td>
                @if($movement->type == 'entrada')
                    {{ $movement->tank->name_location ?? 'Entrada General' }}
                @else
                    Reporte de Ventas ({{ $movement->item_count }} productos)
                @endif
            </td>
            <td class="text-end">{{ number_format($movement->total_volume, 2, ',', '.') }}</td>
            <td class="text-end">{{ $movement->type == 'salida' ? 'Bs. ' . number_format($movement->total_sales, 2, ',', '.') : '-' }}</td>
            <td>{{ $movement->user->name }}</td>
            <td>
                <span class="badge bg-warning text-dark">{{ ucfirst($movement->status) }}</span>
            </td>
        </tr>
    @empty
        <tr><td colspan="8" class="text-center">No se han registrado movimientos.</td></tr>
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