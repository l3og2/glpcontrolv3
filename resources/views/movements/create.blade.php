@extends('layouts.app')

@section('title', 'Registrar Movimiento')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3>Registrar Nuevo Movimiento de Inventario</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('movements.store') }}" method="POST">
            @csrf

            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="row">
                
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Tipo de Movimiento</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="" disabled selected>Seleccione una opción...</option>
                        <option value="entrada" {{ old('type') == 'entrada' ? 'selected' : '' }}>Entrada (Compra)</option>
                        <option value="salida" {{ old('type') == 'salida' ? 'selected' : '' }}>Salida (Venta)</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="movement_date" class="form-label">Fecha y Hora del Movimiento</label>
                    <input type="datetime-local" name="movement_date" class="form-control" value="{{ old('movement_date', now()->toDateTimeLocalString()) }}" required>
                </div>
            </div>
            
            
            <div id="entrada_fields" class="d-none">
                <h5 class="mt-3">Detalles de la Entrada</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tank_id" class="form-label">Tanque de Destino</label>
                        <select name="tank_id" class="form-select">
                            @foreach($tanks as $tank)
                                <option value="{{ $tank->id }}" {{ old('tank_id') == $tank->id ? 'selected' : '' }}>{{ $tank->name_location }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            
            <div id="salida_fields" class="d-none">
                <h5 class="mt-3">Detalles de la Salida</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">Producto Vendido</label>
                        <select name="product_id" class="form-select">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="client_id" class="form-label">Cliente (Opcional)</label>
                        <select name="client_id" class="form-select">
                            <option value="">Venta general...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <hr>

            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="volume_liters" class="form-label">Volumen en Litros</label>
                    <input type="number" name="volume_liters" class="form-control" step="0.01" placeholder="Ej: 1500.50" value="{{ old('volume_liters') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="notes" class="form-label">Notas Adicionales</label>
                    <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('movements.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-corporate">Guardar Movimiento</button>
            </div>
        </form>
    </div>
</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const entradaFields = document.getElementById('entrada_fields');
        const salidaFields = document.getElementById('salida_fields');

        function toggleFields() {
            if (typeSelect.value === 'entrada') {
                entradaFields.classList.remove('d-none');
                salidaFields.classList.add('d-none');
            } else if (typeSelect.value === 'salida') {
                entradaFields.classList.add('d-none');
                salidaFields.classList.remove('d-none');
            } else {
                entradaFields.classList.add('d-none');
                salidaFields.classList.add('d-none');
            }
        }

        // Ejecutar al cargar la página por si hay un valor 'old'
        toggleFields(); 

        // Ejecutar cada vez que cambie el selector
        typeSelect.addEventListener('change', toggleFields);
    });
</script>
@endpush
@endsection