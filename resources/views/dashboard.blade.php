@extends('layouts.app')

@section('title', 'Dashboard Gerencial')

@section('content')
    <!-- 1. Tarjetas de Indicadores Clave (KPIs) -->
    <div class="row">
        <!-- Tarjeta 1: Ventas Totales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ventas Totales (Mes Actual)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bs. {{ number_format($kpis['total_sales'], 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Volumen Despachado -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                     <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Volumen Despachado (Mes)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($kpis['volume_dispatch'], 2, ',', '.') }} Lts</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Entradas Totales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Entradas Totales (Mes)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($kpis['volume_entry'], 2, ',', '.') }} Lts</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Movimientos Pendientes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Movimientos Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $kpis['pending_movements'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Gráfico y Tablas -->
    <div class="row">
        <!-- Gráfico de Tendencias -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold corporate-red">Tendencia de Entradas vs. Salidas (Últimos 6 Meses)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Productos y Acciones Rápidas -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold corporate-red">Top 5 Productos Vendidos (Mes Actual)</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Producto</th><th class="text-end">Volumen (Lts)</th></tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($item->total_volume, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2">No hay datos de ventas este mes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trendsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar', // Puedes cambiarlo a 'line' para otro estilo de gráfico
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
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>
@endpush