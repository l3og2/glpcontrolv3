@extends('layouts.app')

@section('title', 'Resultados del Reporte')

@section('header-actions')
    <div>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle me-1"></i> Volver a Filtros
        </a>

        <!-- Botón de Exportar que usa un formulario oculto -->
        <form action="{{ route('reports.export') }}" method="POST" class="d-inline">
            @csrf
            {{-- Pasamos los mismos filtros al controlador de exportación --}}
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <input type="hidden" name="state_id" value="{{ request('state_id') }}">
            <input type="hidden" name="type" value="{{ request('type') }}">
            <input type="hidden" name="product_id" value="{{ request('product_id') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            
            <button type="submit" class="btn btn-success">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar a Excel
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3>Resultados del Reporte</h3>
        <p class="mb-0 text-muted">
            Mostrando movimientos desde el <strong>{{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}</strong> hasta el <strong>{{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}</strong>.
        </p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>N° Control</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th class="text-end">Volumen (Lts)</th>
                        <th class="text-end">Monto Total (Bs.)</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Cantidades de Salida</th>
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
                            <td>
                                @if($movement->type == 'entrada')
                                    {{ $movement->tank->name_location ?? 'N/A' }}
                                @else
                                    {{ $movement->product->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($movement->volume_liters, 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($movement->total_amount, 2, ',', '.') }}</td>
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
                            <td class="text-center">
                                {{ $movement->type == 'salida' ? $movement->quantity : '-' }}
                            </td>                    
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No se encontraron movimientos que coincidan con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if ($movements->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{-- Los links de paginación mantendrán los filtros gracias a withQueryString() --}}
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection