@extends('layouts.app')
@section('title', 'Registro de Salidas por Lote')

@section('content')
<form action="{{-- route('movements.store_batch') --}}" method="POST">
    @csrf
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Reporte de Ventas Diarias</h3>
            <div>
                <label for="movement_date">Fecha del Reporte:</label>
                <input type="date" id="movement_date" name="movement_date" class="form-control d-inline-block w-auto" value="{{ now()->format('Y-m-d') }}">
            </div>
        </div>
        <div class="card-body">
            
            @php
                // Agrupamos productos por tipo de tarifa (Residencial, Comercial, Convenio)
                $groupedProducts = $products->groupBy(function ($item) {
                    if (str_contains($item->name, '-Res')) return 'Residencial';
                    if (str_contains($item->name, '-Com')) return 'Comercial';
                    return 'Convenios'; // Asumimos que el resto es Convenio
                });
            @endphp

            @foreach ($groupedProducts as $groupName => $productsInGroup)
            <h4 class="mt-4">GLP {{ $groupName }}</h4>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Cantidades de Salida</th>
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
                                <!-- El único campo editable por el usuario -->
                                <input type="number" name="quantities[{{ $product->id }}]" class="form-control table-input quantity-input" min="0">
                            </td>
                            <td class="text-start">{{ str_replace(['-Res', '-Com', '-Conv'], '', $product->name) }}</td>
                            <td>{{ $product->weight_kg ?? '-' }}</td>
                            <td class="volume-per-unit">{{ $product->volume_liters }}</td>
                            <td class="price-per-unit">{{ number_format($prices[$product->id] ?? 0, 2) }}</td>
                            <td class="table-output total-volume">0.00</td>
                            <td class="table-output total-sales">0.00</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-total">
                            <td colspan="5" class="text-end fw-bold">TOTAL GLP {{ strtoupper($groupName) }}</td>
                            <td id="subtotal-volume-{{ $groupName }}" class="fw-bold">0.00</td>
                            <td id="subtotal-sales-{{ $groupName }}" class="fw-bold">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endforeach

            <!-- GRANDES TOTALES -->
            <table class="table mt-5">
                <tbody>
                    <tr class="table-grand-total fs-5">
                        <td class="text-end">TOTAL VOLUMEN DE SALIDA (LITROS)</td>
                        <td id="grand-total-volume" style="width: 20%;">0.00</td>
                    </tr>
                    <tr class="table-grand-total fs-5">
                        <td class="text-end">TOTAL VENTAS</td>
                        <td id="grand-total-sales" style="width: 20%;">0.00</td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="card-footer text-end">
            <a href="{{ route('movements.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-corporate">Guardar Reporte de Ventas</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    function calculateRow(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const volumePerUnit = parseFloat(row.querySelector('.volume-per-unit').textContent) || 0;
        const pricePerUnit = parseFloat(row.querySelector('.price-per-unit').textContent.replace(',', '')) || 0;

        const totalVolume = quantity * volumePerUnit;
        const totalSales = quantity * pricePerUnit;

        row.querySelector('.total-volume').textContent = totalVolume.toFixed(2);
        row.querySelector('.total-sales').textContent = totalSales.toFixed(2);
    }

    function calculateTotals() {
        let grandTotalVolume = 0;
        let grandTotalSales = 0;
        
        document.querySelectorAll('.calculation-group').forEach(group => {
            let subtotalVolume = 0;
            let subtotalSales = 0;
            const groupName = group.dataset.groupName;

            group.querySelectorAll('.product-row').forEach(row => {
                subtotalVolume += parseFloat(row.querySelector('.total-volume').textContent) || 0;
                subtotalSales += parseFloat(row.querySelector('.total-sales').textContent) || 0;
            });
            
            document.getElementById(`subtotal-volume-${groupName}`).textContent = subtotalVolume.toFixed(2);
            document.getElementById(`subtotal-sales-${groupName}`).textContent = subtotalSales.toFixed(2);

            grandTotalVolume += subtotalVolume;
            grandTotalSales += subtotalSales;
        });

        document.getElementById('grand-total-volume').textContent = grandTotalVolume.toFixed(2);
        document.getElementById('grand-total-sales').textContent = grandTotalSales.toFixed(2);
    }

    form.addEventListener('input', function(event) {
        if (event.target.classList.contains('quantity-input')) {
            const row = event.target.closest('.product-row');
            calculateRow(row);
            calculateTotals();
        }
    });

    // Initial calculation in case of old values
    document.querySelectorAll('.product-row').forEach(calculateRow);
    calculateTotals();
});
</script>
@endpush

@endsection