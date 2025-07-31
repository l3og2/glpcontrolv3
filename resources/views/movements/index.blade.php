@extends('layouts.app')

@section('title', 'Listado de Movimientos de Inventario')

@section('header-actions')
    <!-- Ponemos los botones de acción en la cabecera, como definimos en el layout app.blade.php -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        @can('create movements')
            <a href="{{ route('movements.create') }}" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-down me-1"></i> Registrar Entrada
            </a>
            <a href="{{ route('movements.create_batch_salida') }}" class="btn btn-corporate">
                <i class="bi bi-box-arrow-up me-1"></i> Registrar Salida (Lote)
            </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>N° Control / Lote</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th class="text-end">Volumen (Lts)</th>
                        <th class="text-end">Total Ventas</th>
                        <th>Registrado Por</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        {{-- ==================== INICIO DE LA CORRECCIÓN ==================== --}}
                        {{-- Hacemos que toda la fila sea un enlace al detalle del lote, excepto la última celda de acciones. --}}
                        <tr class="movement-row" data-href="{{ route('movements.show_batch', ['batch_id' => $movement->batch_id]) }}" style="cursor: pointer;">
                            <td>
                                <strong>{{ $movement->first_control_number }}</strong>
                                {{-- Mostramos la palabra "Lote:" solo si hay más de un item --}}
                                @if($movement->item_count > 1)
                                    <br>
                                    <small class="text-muted">Lote: {{ substr($movement->batch_id, 0, 8) }}</small>
                                @endif
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
                            <td class="text-center">
                                @php
                                    $statusColor = match($movement->status) {
                                        'ingresado' => 'warning',
                                        'revisado' => 'info',
                                        'aprobado' => 'success',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} text-dark">{{ ucfirst($movement->status) }}</span>
                            </td>
                            {{-- Esta celda de acciones se deja fuera del enlace para que los botones funcionen por separado --}}
                            <td class="text-center actions-cell">
                                @if($movement->status == 'ingresado')
                                    @can('review movements')
                                        <form action="{{-- route('movements.review', $movement->first_control_number) --}}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-info" title="Revisar Movimiento">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                @if($movement->status == 'revisado')
                                    @can('approve movements')
                                         <form action="{{-- route('movements.approve', $movement->first_control_number) --}}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" title="Aprobar Movimiento">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-4">No se han registrado movimientos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if ($movements->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Script para hacer las filas clickables --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.movement-row');
    rows.forEach(row => {
        row.addEventListener('click', function(event) {
            // Previene que el click en los botones del formulario active la navegación
            if (event.target.closest('.actions-cell')) {
                return;
            }
            window.location.href = this.dataset.href;
        });
    });
});
</script>
@endpush

@endsection