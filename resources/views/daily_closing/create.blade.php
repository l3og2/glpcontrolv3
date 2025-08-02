@extends('layouts.app')
@section('title', 'Cierre de Inventario Diario')

@section('header-actions')
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
        <i class="bi bi-x-circle me-1"></i> Cancelar
    </a>
@endsection

@section('content')
<form action="{{ route('daily-closing.store') }}" method="POST">
    @csrf
    <div class="card shadow-sm">
        <div class="card-header">
            <h3>Cierre Diario - {{ $today->format('d/m/Y') }}</h3>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
            <div class="alert alert-danger">
                <h5 class="alert-heading">Error de Validación</h5>
                <ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
            @endif

            <div class="row text-center">
                <div class="col-md-4">
                    <div class="text-muted">Inventario Final de Ayer</div>
                    <p class="fs-4 fw-bold">{{ number_format($closingData['initial_inventory'], 2, ',', '.') }} Lts</p>
                </div>
                <div class="col-md-4">
                    <div class="text-muted">(+) Entradas Aprobadas del Día</div>
                    <p class="fs-4 fw-bold text-success">{{ number_format($closingData['total_entries'], 2, ',', '.') }} Lts</p>
                </div>
                <div class="col-md-4">
                    <div class="text-muted">(-) Salidas Aprobadas del Día</div>
                    <p class="fs-4 fw-bold text-danger">{{ number_format($closingData['total_exits'], 2, ',', '.') }} Lts</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label for="theorical_inventory_display" class="form-label">Inventario Teórico (Calculado):</label>
                    <!-- Input oculto para guardar el valor numérico puro para JS -->
                    <input type="hidden" id="theorical_inventory_value" value="{{ $closingData['theorical_inventory'] }}">
                    <!-- Input visible formateado para el usuario -->
                    <input type="text" id="theorical_inventory_display" class="form-control form-control-lg bg-light" value="{{ number_format($closingData['theorical_inventory'], 2, ',', '.') }}" readonly>
                </div>
                <div class="col-md-4">
                    <label for="manual_reading" class="form-label">Lectura Manual de Tanques (Lts):</label>
                    <!-- El input es de tipo "text" para permitir comas y puntos -->
                    <input type="text" id="manual_reading" name="manual_reading" class="form-control form-control-lg @error('manual_reading') is-invalid @enderror" value="{{ old('manual_reading') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Discrepancia:</label>
                    <p id="discrepancy_display" class="fs-4 fw-bold mb-0">0,00 Lts (0,00%)</p>
                </div>
            </div>
            <div class="mt-3" id="justification_div" style="display: none;">
                <label for="justification" class="form-label">Justificación de la Discrepancia:</label>
                <textarea name="justification" class="form-control" rows="3" placeholder="Explique el motivo de la diferencia de inventario...">{{ old('justification') }}</textarea>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="submit" id="submit_button" class="btn btn-corporate btn-lg">Confirmar Cierre del Día</button>
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

    // Función para convertir un string formateado (ej: "1.234,56" o "1234.56") a un número puro
    function parseLocalNumber(stringNumber) {
        if (!stringNumber) return 0;
        // Primero eliminamos los separadores de miles (puntos), luego reemplazamos la coma decimal por un punto
        return parseFloat(stringNumber.replace(/\./g, '').replace(',', '.')) || 0;
    }
    
    // Función para formatear un número a nuestro estilo local (ej: 1234.56 -> "1.234,56")
    function formatLocalNumber(number) {
        return new Intl.NumberFormat('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
    }

    function calculateDiscrepancy() {
        // Leemos el valor puro del input oculto
        const theorical = parseFloat(theoricalValueInput.value) || 0;
        // Convertimos el valor ingresado por el usuario a un número puro
        const manual = parseLocalNumber(manualInput.value);
        
        const discrepancy = manual - theorical;
        const percentage = theorical !== 0 ? Math.abs((discrepancy / theorical) * 100) : (manual > 0 ? 100 : 0);
        
        const discrepancyColor = percentage > 3 ? 'text-danger' : 'text-success';
        display.innerHTML = `${formatLocalNumber(discrepancy)} Lts <strong class="${discrepancyColor}">(${formatLocalNumber(percentage)}%)</strong>`;
        
        if (percentage > 3) {
            submitBtn.disabled = true;
            submitBtn.title = 'La discrepancia supera el 3% permitido. Se requiere justificación.';
            justificationDiv.style.display = 'block';
        } else {
            submitBtn.disabled = false;
            submitBtn.title = '';
            justificationDiv.style.display = 'none';
        }
    }

    // Escuchamos el evento 'input' para recalcular en tiempo real
    manualInput.addEventListener('input', calculateDiscrepancy);
    
    // Ejecutamos un cálculo inicial al cargar la página por si hay valores "old"
    calculateDiscrepancy();
});
</script>
@endpush