@extends('layouts.app')

@section('title', 'Generador de Reportes')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3>Generar Reporte de Movimientos</h3>
    </div>
    <div class="card-body">
        <p class="card-subtitle mb-4 text-muted">Seleccione los filtros deseados para generar un reporte detallado de los movimientos de inventario.</p>
        
        <form action="{{ route('reports.generate') }}" method="GET">
            
            <div class="row g-3">
                
                <!-- Rango de Fechas -->
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Fecha de Inicio:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="end_date" class="form-label">Fecha de Fin:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ old('end_date', now()->format('Y-m-d')) }}" required>
                </div>

                <!-- Filtro de Estado (Solo para Admins) -->
                @role('Admin')
                <div class="col-md-6">
                    <label for="state_id" class="form-label">Estado:</label>
                    <select id="state_id" name="state_id" class="form-select">
                        <option value="">-- Todos los Estados --</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endrole

                <!-- Tipo de Movimiento -->
                <div class="col-md-6">
                    <label for="type" class="form-label">Tipo de Movimiento:</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">-- Todos los Tipos --</option>
                        <option value="entrada" {{ old('type') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="salida" {{ old('type') == 'salida' ? 'selected' : '' }}>Salida</option>
                    </select>
                </div>

                <!-- Producto -->
                <div class="col-md-6">
                    <label for="product_id" class="form-label">Producto:</label>
                    <select id="product_id" name="product_id" class="form-select">
                        <option value="">-- Todos los Productos --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Estado del Movimiento -->
                 <div class="col-md-6">
                    <label for="status" class="form-label">Estado del Movimiento:</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">-- Todos los Estados --</option>
                        <option value="ingresado" {{ old('status') == 'ingresado' ? 'selected' : '' }}>Ingresado</option>
                        <option value="revisado" {{ old('status') == 'revisado' ? 'selected' : '' }}>Revisado</option>
                        <option value="aprobado" {{ old('status') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                    </select>
                </div>

            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-search me-1"></i> Generar Reporte
                </button>
            </div>
        </form>
    </div>
</div>
@endsection