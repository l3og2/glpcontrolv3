@extends('layouts.app')

@section('title', 'Dashboard Gerencial')

@section('header-actions')
    <!-- Sección de Acciones Rápidas en la cabecera de la página -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        
        <!-- Botón para Cierre Diario (Visible solo para roles con el permiso) -->
        @can('perform daily closing')
            <a href="{{ route('daily-closing.create') }}" class="btn btn-warning fw-bold">
                <i class="bi bi-calendar2-check-fill me-1"></i> Realizar Cierre Diario
            </a>
        @endcan

        <!-- Botones para registrar movimientos (Visibles solo para roles con el permiso) -->
        @can('create movements')
            <a href="{{ route('movements.create') }}" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-down me-1"></i> Registrar Entrada
            </a>
            <a href="{{ route('movements.create_batch_salida') }}" class="btn btn-corporate">
                <i class="bi bi-box-arrow-up me-1"></i> Registrar Salida
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <!-- 1. Tarjetas de Indicadores Clave (KPIs) -->
    <div class="row">
        <!-- Tarjeta 1: Ventas Totales -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-white bg-primary shadow h-100">
                <div class="card-body">
                    <div class="text-uppercase mb-1 small">Ventas (Mes Actual)</div>
                    <div class="h4 mb-0 fw-bold">Bs. {{ number_format($kpis['total_sales'], 2, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Volumen Despachado -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-white bg-success shadow h-100">
                <div class="card-body">
                    <div class="text-uppercase mb-1 small">Volumen Despachado (Mes)</div>
                    <div class="h4 mb-0 fw-bold">{{ number_format($kpis['volume_dispatch'], 2, ',', '.') }} Lts</div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Entradas Totales -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-white bg-info shadow h-100">
                <div class="card-body">
                    <div class="text-uppercase mb-1 small">Entradas Totales (Mes)</div>
                    <div class="h4 mb-0 fw-bold">{{ number_format($kpis['volume_entry'], 2, ',', '.') }} Lts</div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Movimientos Pendientes -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-dark bg-warning shadow h-100">
                <div class="card-body">
                    <div class="text-uppercase mb-1 small">Movimientos Pendientes</div>
                    <div class="h4 mb-0 fw-bold">{{ $kpis['pending_movements'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Gráfico y Tablas -->
    <div class="row">
        <!-- Gráfico de Tendencias -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h5 class="m-0 corporate-red">Tendencia de Entradas vs. Salidas (Últimos 6 Meses)</h5>
                </div>
                <div class="card-body">
                    <div style="height: 320px;">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Productos Vendidos -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h5 class="m-0 corporate-red">Top 5 Productos Vendidos (Mes Actual)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Volumen (Lts)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($item->total_volume, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted p-3">No hay datos de ventas este mes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Incluimos Chart.js desde un CDN para simplicidad --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trendsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Volumen de Entradas (Lts)',
                        data: {!! json_encode($chartDataEntradas) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Volumen de Salidas (Lts)',
                        data: {!! json_encode($chartDataSalidas) !!},
                        backgroundColor: 'rgba(200, 0, 0, 0.5)', // Rojo corporativo
                        borderColor: 'rgba(200, 0, 0, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    });
</script>
@endpush