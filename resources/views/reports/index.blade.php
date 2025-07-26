@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-4">Reporte de Movimientos</h1>

    <!-- Filtros -->
    <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label class="form-label">Desde</label>
            <input type="date" name="start_date" value="{{ $start }}" class="form-control" />
        </div>
        <div class="col-md-3">
            <label class="form-label">Hasta</label>
            <input type="date" name="end_date" value="{{ $end }}" class="form-control" />
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipo de Movimiento</label>
            <select name="type" class="form-select">
                <option value="">-- Todos --</option>
                <option value="entrada" {{ $type == 'entrada' ? 'selected' : '' }}>Entrada</option>
                <option value="salida" {{ $type == 'salida' ? 'selected' : '' }}>Salida</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="{{ route('reports.export.excel', ['start_date' => $start, 'end_date' => $end, 'type' => $type]) }}"
               class="btn btn-success">
                Exportar Excel
            </a>
        </div>
    </form>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ ucfirst($movement->type) }}</td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->user->name ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">No hay movimientos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-3">
        {{ $movements->withQueryString()->links() }}
    </div>
</div>
@endsection
