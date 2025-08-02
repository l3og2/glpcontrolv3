@extends('layouts.app')
@section('title', 'Comprobante de Movimiento')

@section('header-actions')
    <div>
        <a href="{{ route('movements.index') }}" class="btn btn-secondary">← Volver al Listado</a>
        <button onclick="window.print();" class="btn btn-outline-primary">
            <i class="bi bi-printer-fill me-1"></i> Imprimir
        </button>
    </div>
@endsection

@section('content')
    {{-- =============================================== --}}
    {{-- TODO EL CONTENIDO DE LA VISTA DEBE IR DENTRO --}}
    {{-- DE LAS DIRECTIVAS @section                       --}}
    {{-- =============================================== --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Comprobante de {{ ucfirst($reference->type) }}</h4>
                    <p class="mb-0 text-muted">Fecha de Operación: <strong>{{ $reference->movement_date->format('d/m/Y') }}</strong></p>
                </div>
                <div class="text-end">
                    <p class="mb-0 text-muted">ID de Lote:</p>
                    <h5 class="mb-0"><strong class="corporate-red font-monospace">{{ substr($reference->batch_id, 0, 8) }}</strong></h5>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            
            @if($reference->type == 'salida')
                {{-- VISTA PARA UN LOTE DE SALIDA --}}
                @php
                    $groupedMovements = $movements->groupBy(function ($item) {
                        if (str_contains($item->product->name, '-Res')) return 'Residencial';
                        if (str_contains($item->product->name, '-Com')) return 'Comercial';
                        return 'Convenios';
                    });
                @endphp

                @foreach ($groupedMovements as $groupName => $movementsInGroup)
                <h5 class="mt-4 mb-3">GLP {{ $groupName }}</h5>
                <div class="table-responsive">
                    <table class="table table-bordered text-center table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Cantidad Vendida</th>
                                <th class="text-start">Producto</th>
                                <th class="text-end">Precio Unitario</th>
                                <th class="text-end">Total Volumen (Lts)</th>
                                <th class="text-end">Monto Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movementsInGroup as $movement)
                            <tr>
                                <td><strong>{{ $movement->quantity }}</strong></td>
                                <td class="text-start">{{ str_replace(['-Res', '-Com', '-Conv'], '', $movement->product->name) }}</td>
                                <td class="text-end">Bs. {{ number_format($movement->unit_price, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($movement->volume_liters, 2, ',', '.') }}</td>
                                <td class="text-end">Bs. {{ number_format($movement->total_amount, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <td colspan="3" class="text-end">TOTAL GLP {{ strtoupper($groupName) }}</td>
                                <td class="text-end">{{ number_format($movementsInGroup->sum('volume_liters'), 2, ',', '.') }}</td>
                                <td class="text-end">Bs. {{ number_format($movementsInGroup->sum('total_amount'), 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endforeach

                <!-- GRANDES TOTALES -->
                <div class="row justify-content-end mt-4">
                    <div class="col-md-6">
                        <table class="table">
                             <tbody>
                                <tr class="table-dark fs-5">
                                    <td class="text-end"><strong>TOTAL VOLUMEN DE SALIDA (LITROS)</strong></td>
                                    <td class="text-end" style="width: 30%;"><strong>{{ number_format($movements->sum('volume_liters'), 2, ',', '.') }}</strong></td>
                                </tr>
                                <tr class="table-dark fs-5">
                                    <td class="text-end"><strong>TOTAL VENTAS</strong></td>
                                    <td class="text-end" style="width: 30%;"><strong>Bs. {{ number_format($movements->sum('total_amount'), 2, ',', '.') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            
            @else
                {{-- VISTA PARA UNA ENTRADA (LOTE DE 1) --}}
                <h5 class="mb-3">Detalle de la Orden de Llenado</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>N° de Control:</strong> {{ $reference->control_number }}</p>
                        <p class="mb-2"><strong>Fuente de Suministro:</strong> {{ $reference->supply_source ?? 'No especificada' }}</p>
                        <p class="mb-2"><strong>N° de Venta PDVSA:</strong> {{ $reference->pdvsa_sale_number ?? 'No especificado' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Tanque de Destino:</strong> {{ $reference->tank->name_location ?? 'No especificado' }}</p>
                        <p class="mb-2"><strong>Conductor:</strong> {{ $reference->driver_name ?? 'No especificado' }} (C.I: {{ $reference->driver_ci ?? 'N/A' }})</p>
                        <p class="mb-2"><strong>Cisterna:</strong> Placa {{ $reference->cisterna_plate ?? 'N/A' }} / Capacidad {{ number_format($reference->cisterna_capacity_gallons, 0) }} Gal</p>
                    </div>
                </div>
                <div class="alert alert-success text-center mt-3">
                    <h4 class="alert-heading">VOLUMEN RECIBIDO</h4>
                    <p class="display-6 mb-0">{{ number_format($reference->volume_liters, 2, ',', '.') }} Litros</p>
                </div>
            @endif

            <hr class="my-4">
            <p class="text-muted small mb-0">
                Registrado por <strong>{{ $reference->user->name }}</strong> 
                en el estado de <strong>{{ $reference->state->name }}</strong> 
                el <strong>{{ $reference->created_at->format('d/m/Y \a \l\a\s H:i A') }}</strong>. 
                Estado actual del movimiento: <strong>{{ ucfirst($reference->status) }}</strong>.
            </p>
        </div>
    </div>
@endsection