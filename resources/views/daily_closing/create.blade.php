@extends('layouts.app')
@section('title', 'Resumen Cierre Diario')

@section('header-actions')
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left-circle me-1"></i> Volver al Dashboard
    </a>
@endsection

@section('content')
<form action="{{ route('daily-closing.store') }}" method="POST">
    @csrf
    <div class="card shadow-sm">
        <div class="card-header text-center">
            <h3 class="mb-0">RESUMEN CIERRE DIARIO</h3>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
            <div class="alert alert-danger">
                <h5 class="alert-heading">Error de Validación</h5>
                <ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-4"><strong>Inventario Inicial (Litros):</strong> {{ number_format($closingData['initial_inventory'], 2, ',', '.') }}</div>
                <div class="col-md-4 text-center"><strong>FECHA:</strong> {{ $today->format('d/m/Y') }}</div>
                <div class="col-md-4 text-end"><strong>ESTADO:</strong> {{ auth()->user()->state->name }}</div>
            </div>
            
            <div class="row">
                @foreach(['Residencial', 'Comercial', 'Convenios'] as $category)
                <div class="col-md-4">
                    <h6 class="text-center fw-bold">Resumen Ventas GLP {{ $category }}</h6>
                    <table class="table table-sm table-bordered">
                        <thead><tr><th>Productos</th><th>Cantidades</th><th>Total Volumen</th><th>Total Ventas</th></tr></thead>
                        <tbody>
                        @foreach($closingData['all_products'][$category] ?? [] as $product)
                            @php
                                $summary = ($closingData['sales_summary'][$category] ?? collect())->firstWhere('product_name', $product->name);
                            @endphp
                            <tr>
                                <td>{{ str_replace(['Cilindros ', ' Litro', "-Res", "-Com", "-Conv"], '', $product->name) }}</td>
                                <td class="text-center">{{ $summary['quantity'] ?? '-' }}</td>
                                <td class="text-end">{{ $summary ? number_format($summary['total_volume'], 2, ',', '.') : '-' }}</td>
                                <td class="text-end">{{ $summary ? number_format($summary['total_sales'], 2, ',', '.') : '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-group-divider fw-bold">
                            <tr>
                                <td class="text-end">Total {{ $category }}</td>
                                <td class="text-center">{{ ($closingData['sales_summary'][$category] ?? collect())->sum('quantity') }}</td>
                                <td class="text-end">{{ number_format(($closingData['sales_summary'][$category] ?? collect())->sum('total_volume'), 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format(($closingData['sales_summary'][$category] ?? collect())->sum('total_sales'), 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endforeach
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-md-10">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Entradas Aprobadas (+)</th>
                                <th>Salidas Aprobadas (-)</th>
                                <th>Total Cantidades Salida (Cilindros)</th>
                                <th>Total Ventas Bs.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="fw-bold">
                                <td class="text-success fs-5">{{ number_format($closingData['total_entries'], 2, ',', '.') }}</td>
                                <td class="text-danger fs-5">{{ number_format($closingData['total_exits'], 2, ',', '.') }}</td>
                                {{-- Usamos el nuevo total de cilindros --}}
                                <td class="fs-5">{{ $closingData['total_cylinder_units'] ?? 0 }}</td>
                                <td class="fs-5">{{ number_format($closingData['sales_summary']->flatten(1)->sum('total_sales') ?? 0, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <p><strong>Inventario Teórico (Litros):</strong> <span id="theorical_inventory_display">{{ number_format($closingData['theorical_inventory'], 2, ',', '.') }}</span></p>
                            <!-- Input oculto con el valor numérico puro para JS -->
                            <input type="hidden" id="theorical_inventory_value" value="{{ $closingData['theorical_inventory'] }}">
                            
                            <div class="mb-3">
                                <label for="manual_reading" class="form-label fw-bold">Lectura Manual (Litros):</label>
                                <input type="text" id="manual_reading" name="manual_reading" class="form-control" value="{{ old('manual_reading') }}" required>
                            </div>
                            <p><strong>Discrepancia (Litros):</strong> <span id="discrepancy_display">...</span></p>
                             <div class="mt-3" id="justification_div" style="display: none;">
                                <label for="justification" class="form-label">Justificación de la Discrepancia:</label>
                                <textarea name="justification" class="form-control" rows="3" placeholder="Explique el motivo de la diferencia de inventario...">{{ old('justification') }}</textarea>
                            </div>
                        </div>
                         <div class="col-md-4 d-flex align-items-end">
                             <button type="submit" id="submit_button" class="btn btn-corporate w-100">Confirmar Cierre del Día</button>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const theoricalValueInput = document.getElementById('theorical_inventory_value');
    const manualInput = document.getElementById('manual_reading');
    const display = document.getElementById('discrepancy_display');
    const submitBtn = document.getElementById('submit_button');
    const justificationDiv = document.getElementById('justification_div');

    function parseLocalNumber(stringNumber) {
        if (!stringNumber) return 0;
        return parseFloat(stringNumber.replace(/\./g, '').replace(',', '.')) || 0;
    }
    
    function formatLocalNumber(number) {
        return new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
    }

    function calculateDiscrepancy() {
        const theorical = parseFloat(theoricalValueInput.value) || 0;
        const manual = parseLocalNumber(manualInput.value);
        
        const discrepancy = manual - theorical;
        const percentage = theorical !== 0 ? Math.abs((discrepancy / theorical) * 100) : (manual > 0 ? 100 : 0);
        
        const discrepancyColor = percentage > 1 ? 'text-danger' : 'text-success';
        
        display.innerHTML = `${formatLocalNumber(discrepancy)} Lts <strong class="${discrepancyColor}">(${formatLocalNumber(percentage)}%)</strong>`;
        
        if (percentage > 1) {
            submitBtn.disabled = true;
            submitBtn.title = 'La discrepancia supera el 1% permitido.';
            if (justificationDiv) justificationDiv.style.display = 'block';
        } else {
            submitBtn.disabled = false;
            submitBtn.title = '';
            if (justificationDiv) justificationDiv.style.display = 'none';
        }
    }

    manualInput.addEventListener('input', calculateDiscrepancy);
    manualInput.addEventListener('blur', calculateDiscrepancy);
    calculateDiscrepancy(); // Initial calculation
});
</script>
@endpush