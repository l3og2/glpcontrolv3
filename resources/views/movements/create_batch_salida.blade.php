@extends('layouts.app')

@section('title', 'Registro de Salidas por Lote')

@section('header-actions')
    <a href="{{ route('movements.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left-circle me-1"></i> Cancelar y Volver al Listado
    </a>
@endsection

@section('content')
<form action="{{ route('movements.store_batch') }}" method="POST">
    @csrf
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Reporte de Ventas Diarias</h3>
            <div class="d-flex align-items-center">
                <label for="movement_date" class="form-label mb-0 me-2">Fecha del Reporte:</label>
                <input type="date" id="movement_date" name="movement_date" class="form-control" style="width: auto;" value="{{ now()->format('Y-m-d') }}">
            </div>
        </div>
        <div class="card-body">
            
            @php
                // Agrupamos productos por tipo de tarifa (Residencial, Comercial, Convenio)
                $groupedProducts = $products->groupBy(function ($item) {
                    if (str_contains($item->name, '-Res')) return 'Residencial';
                    if (str_contains($item->name, '-Com')) return 'Comercial';
                    return 'Convenios';
                });
            @endphp

            @foreach ($groupedProducts as $groupName => $productsInGroup)
            <h4 class="mt-4">GLP {{ $groupName }}</h4>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Cantidades de Salida</th>
                            <th>Productos</th>
                            <th>Peso (KG)</th>
                            <th>Volumen (Litros)</th>
                            <th>Precio (Según Tarifas)</th>
                            <th>Total Volumen (Litros)</th>
                            <th>Total Ventas</th>
                        </tr>
                    </thead>
                    <tbody class="calculation-group" data-group-name="{{ $groupName }}">
                        @foreach ($productsInGroup as $product)
                        <tr class="product-row">
                            <td>
                                <input type="number" name="quantities[{{ $product->id }}]" class="form-control table-input quantity-input" min="0" placeholder="0">
                            </td>
                            <td class="text-start">{{ str_replace(['-Res', '-Com', '-Conv'], '', $product->name) }}</td>
                            <td>{{ $product->weight_kg ?? '-' }}</td>
                            
                            {{-- ==================== INICIO DE LA CORRECCIÓN CLAVE ==================== --}}
                            {{-- Forzamos el formato con punto decimal para que JS lo entienda --}}
                            <td class="volume-per-unit">{{ number_format($product->volume_liters, 2, '.', '') }}</td>
                            <td class="price-per-unit">{{ number_format($prices[$product->id] ?? 0, 2, '.', '') }}</td>
                            {{-- ===================== FIN DE LA CORRECCIÓN CLAVE ====================== --}}
                            
                            <td class="table-output total-volume">0.00</td>
                            <td class="table-output total-sales">0.00</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-group-divider">
                        <tr class="table-secondary">
                            <td colspan="5" class="text-end fw-bold">TOTAL GLP {{ strtoupper($groupName) }}</td>
                            <td id="subtotal-volume-{{ $groupName }}" class="fw-bold">0.00</td>
                            <td id="subtotal-sales-{{ $groupName }}" class="fw-bold">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endforeach

            <!-- GRANDES TOTALES -->
            <div class="row justify-content-end mt-4">
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr class="table-dark fs-5">
                                <td class="text-end"><strong>TOTAL VOLUMEN DE SALIDA (LITROS)</strong></td>
                                <td id="grand-total-volume" class="text-end" style="width: 30%;"><strong>0.00</strong></td>
                            </tr>
                            <tr class="table-dark fs-5">
                                <td class="text-end"><strong>TOTAL VENTAS</strong></td>
                                <td id="grand-total-sales" class="text-end" style="width: 30%;"><strong>Bs. 0,00</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-corporate btn-lg">Guardar Reporte de Ventas</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const quantityInputs = document.querySelectorAll('.quantity-input');

    // Funciones de formato para mostrar al usuario final
    function formatCurrency(number) {
        return new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
    }
    function formatNumber(number) {
        return new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
    }

    // Función para calcular los totales de una sola fila
    function calculateRow(inputElement) {
        const row = inputElement.closest('.product-row');
        if (!row) return;

        const quantity = parseFloat(inputElement.value) || 0;
        // Ahora JS puede leer esto directamente porque el HTML tiene formato '19.72'
        const volumePerUnit = parseFloat(row.querySelector('.volume-per-unit').textContent) || 0;
        const pricePerUnit = parseFloat(row.querySelector('.price-per-unit').textContent) || 0;

        const totalVolume = quantity * volumePerUnit;
        const totalSales = quantity * pricePerUnit;
        
        // Guardamos los valores calculados en atributos de datos para facilitar la suma total
        row.querySelector('.total-volume').dataset.value = totalVolume;
        row.querySelector('.total-sales').dataset.value = totalSales;
        
        // Mostramos los valores formateados al usuario
        row.querySelector('.total-volume').textContent = formatNumber(totalVolume);
        row.querySelector('.total-sales').textContent = `Bs. ${formatNumber(totalSales)}`;
    }

    // Función para calcular TODOS los subtotales y grandes totales
    function calculateAllTotals() {
        let grandTotalVolume = 0;
        let grandTotalSales = 0;

        document.querySelectorAll('.calculation-group').forEach(group => {
            let subtotalVolume = 0;
            let subtotalSales = 0;
            const groupName = group.dataset.groupName;

            group.querySelectorAll('.product-row').forEach(row => {
                subtotalVolume += parseFloat(row.querySelector('.total-volume').dataset.value) || 0;
                subtotalSales += parseFloat(row.querySelector('.total-sales').dataset.value) || 0;
            });
            
            document.getElementById(`subtotal-volume-${groupName}`).textContent = formatNumber(subtotalVolume);
            document.getElementById(`subtotal-sales-${groupName}`).textContent = `Bs. ${formatNumber(subtotalSales)}`;

            grandTotalVolume += subtotalVolume;
            grandTotalSales += grandTotalSales;
        });

        document.getElementById('grand-total-volume').textContent = formatNumber(grandTotalVolume);
        document.getElementById('grand-total-sales').textContent = formatCurrency(grandTotalSales);
    }

    quantityInputs.forEach(input => {
        input.addEventListener('input', function() {
            calculateRow(this);
            calculateAllTotals();
        });
    });
    
    // Llamada inicial
    quantityInputs.forEach(calculateRow);
    calculateAllTotals();
});
</script>
@endpush

@endsection