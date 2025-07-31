
@extends('layouts.app')
@section('title', 'Comprobante de Movimiento')

@php
    // Tomamos el primer movimiento como referencia para los datos comunes
    $reference = $movements->first();
@endphp

@section('header-actions')
    <a href="{{ route('movements.index') }}" class="btn btn-secondary">← Volver al Listado</a>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <div>
                <h4>Comprobante de {{ ucfirst($reference->type) }}</h4>
                <p class="mb-0">Fecha de Operación: <strong>{{ $reference->movement_date->format('d/m/Y') }}</strong></p>
            </div>
            <div class="text-end">
                <p class="mb-0">ID de Lote:</p>
                <h5><strong class="corporate-red">{{ substr($reference->batch_id, 0, 8) }}</strong></h5>
            </div>
        </div>
    </div>
    <div class="card-body">
        
        @if($reference->type == 'salida')
            <!-- VISTA PARA UN LOTE DE SALIDA -->
            <h5>Detalle del Reporte de Ventas</h5>
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>N° Control</th>
                        <th>Producto</th>
                        <th class="text-end">Volumen (Lts)</th>
                        <th class="text-end">Precio Unitario</th>
                        <th class="text-end">Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $movement)
                        <tr>
                            <td>{{ $movement->control_number }}</td>
                            <td>{{ $movement->product->name }}</td>
                            <td class="text-end">{{ number_format($movement->volume_liters, 2, ',', '.') }}</td>
                            <td class="text-end">Bs. {{ number_format($movement->unit_price, 2, ',', '.') }}</td>
                            <td class="text-end">Bs. {{ number_format($movement->total_amount, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">Totales:</td>
                        <td class="text-end">{{ number_format($movements->sum('volume_liters'), 2, ',', '.') }}</td>
                        <td></td>
                        <td class="text-end">Bs. {{ number_format($movements->sum('total_amount'), 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <!-- VISTA PARA UNA ENTRADA (LOTE DE 1) -->
            <h5>Detalle de la Orden de Llenado</h5>
            <p><strong>N° de Control:</strong> {{ $reference->control_number }}</p>
            <p><strong>Fuente de Suministro:</strong> {{ $reference->supply_source }}</p>
            <p><strong>Tanque de Destino:</strong> {{ $reference->tank->name_location }}</p>
            <p><strong>Volumen Recibido:</strong> {{ number_format($reference->volume_liters, 2, ',', '.') }} Lts</p>
            <!-- Puedes añadir aquí más detalles de la orden de llenado -->
        @endif

        <hr>
        <p class="text-muted small">
            Registrado por <strong>{{ $reference->user->name }}</strong> 
            en el estado de <strong>{{ $reference->state->name }}</strong> 
            el <strong>{{ $reference->created_at->format('d/m/Y \a \l\a\s H:i') }}</strong>. 
            Estado actual: <strong>{{ ucfirst($reference->status) }}</strong>.
        </p>
    </div>
</div>
@endsection
