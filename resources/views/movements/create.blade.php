@extends('layouts.app')
@section('title', 'Registrar Orden de Llenado')

@section('content')
<form action="{{ route('movements.store') }}" method="POST">
    @csrf
    <!-- Campo oculto para definir el tipo de movimiento como 'entrada' -->
    <input type="hidden" name="type" value="entrada">
    
    <!-- Muestra errores de validación si los hay -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <h5>Por favor, corrige los siguientes errores:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card de Datos Básicos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            ORDEN DE LLENADO DE CISTERNAS - Datos Básicos
        </div>
        <div class="card-body">

    <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="supply_source" class="form-label">Fuente de Suministro</label>
                    <input type="text" id="supply_source" name="supply_source" class="form-control" value="{{ old('supply_source') }}">
                </div>
                
                <!-- ===== INICIO DE LA CORRECCIÓN ===== -->
                <div class="col-md-6 mb-3">
                    <label for="movement_date" class="form-label">Fecha y Hora del Movimiento</label>
                    <input type="datetime-local" 
                           name="movement_date" 
                           id="movement_date"
                           class="form-control @error('movement_date') is-invalid @enderror"
                           value="{{ old('movement_date', now()->toDateTimeString()) }}"
                           required>
                    @error('movement_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <!-- ===== FIN DE LA CORRECCIÓN ===== -->
            </div>
            
    <hr>
            <h5>Chuto</h5>
            <div class="row">
                <div class="col-md-6 mb-3"><label for="chuto_code" class="form-label">Código</label><input type="text" id="chuto_code" name="chuto_code" class="form-control" value="{{ old('chuto_code') }}"></div>
                <div class="col-md-6 mb-3"><label for="chuto_plate" class="form-label">Placa</label><input type="text" id="chuto_plate" name="chuto_plate" class="form-control" value="{{ old('chuto_plate') }}"></div>
            </div>

            <!-- ... resto de los campos ... -->

            <hr>
            <h5>Cisterna</h5>
            <div class="row">
                <div class="col-md-3 mb-3"><label for="cisterna_code" class="form-label">Código</label><input type="text" id="cisterna_code" name="cisterna_code" class="form-control" value="{{ old('cisterna_code') }}"></div>
                <div class="col-md-3 mb-3"><label for="cisterna_capacity_gallons" class="form-label">Capacidad (Gal)</label><input type="number" id="cisterna_capacity_gallons" name="cisterna_capacity_gallons" class="form-control" value="{{ old('cisterna_capacity_gallons') }}"></div>
                <div class="col-md-3 mb-3"><label for="cisterna_plate" class="form-label">Placa</label><input type="text" id="cisterna_plate" name="cisterna_plate" class="form-control" value="{{ old('cisterna_plate') }}"></div>
                <div class="col-md-3 mb-3"><label for="cisterna_serial" class="form-label">Serial Nro.</label><input type="text" id="cisterna_serial" name="cisterna_serial" class="form-control" value="{{ old('cisterna_serial') }}"></div>
            </div>
            
            <hr>
            <h5>Conductor</h5>
            <div class="row">
                <div class="col-md-4 mb-3"><label for="driver_name" class="form-label">Chofer</label><input type="text" id="driver_name" name="driver_name" class="form-control" value="{{ old('driver_name') }}"></div>
                <div class="col-md-4 mb-3"><label for="driver_ci" class="form-label">C.I</label><input type="text" id="driver_ci" name="driver_ci" class="form-control" value="{{ old('driver_ci') }}"></div>
                <div class="col-md-4 mb-3"><label for="driver_code" class="form-label">Código</label><input type="text" id="driver_code" name="driver_code" class="form-control" value="{{ old('driver_code') }}"></div>
            </div>

            <hr>
            <h5>Destino</h5>
            <div class="row">
                 <div class="col-md-12 mb-3">
                    <label for="tank_id" class="form-label">Planta (Tanque de Destino)</label>
                    <select name="tank_id" class="form-select">
                        @foreach($tanks as $tank)
                            <option value="{{ $tank->id }}" {{ old('tank_id') == $tank->id ? 'selected' : '' }}>{{ $tank->name_location }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Card de Lectura de Tanque -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            LECTURA DE TANQUE DE SUMINISTRO
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-8">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr><th>Especificaciones</th><th>Llegada</th><th>Salida</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Volumen %</td><td><input type="number" name="arrival_volume_percentage" class="form-control" value="{{ old('arrival_volume_percentage') }}" step="0.01"></td><td><input type="number" name="departure_volume_percentage" class="form-control" value="{{ old('departure_volume_percentage') }}" step="0.01"></td></tr>
                            <tr><td>Temperatura</td><td><input type="number" name="arrival_temperature" class="form-control" value="{{ old('arrival_temperature') }}" step="0.01"></td><td><input type="number" name="departure_temperature" class="form-control" value="{{ old('departure_temperature') }}" step="0.01"></td></tr>
                            <tr><td>Presión</td><td><input type="number" name="arrival_pressure" class="form-control" value="{{ old('arrival_pressure') }}" step="0.01"></td><td><input type="number" name="departure_pressure" class="form-control" value="{{ old('departure_pressure') }}" step="0.01"></td></tr>
                            <tr><td>Gravedad Específica</td><td><input type="number" name="arrival_specific_gravity" class="form-control" value="{{ old('arrival_specific_gravity') }}" step="0.0001"></td><td><input type="number" name="departure_specific_gravity" class="form-control" value="{{ old('departure_specific_gravity') }}" step="0.0001"></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="pdvsa_sale_number" class="form-label">N° de Venta de PDVSA - Gas</label>
                        <input type="text" id="pdvsa_sale_number" name="pdvsa_sale_number" class="form-control" value="{{ old('pdvsa_sale_number') }}">
                    </div>
                    <div class="mb-3">
                        <label for="volume_liters" class="form-label fw-bold">Litros Netos Despachados</label>
                        <input type="number" id="volume_liters" name="volume_liters" class="form-control form-control-lg" step="0.01" value="{{ old('volume_liters') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botones de Acción -->
    <div class="d-flex justify-content-end mt-3">
        <a href="{{ route('movements.index') }}" class="btn btn-secondary me-2">Cancelar</a>
        <button type="submit" class="btn btn-corporate">Guardar Orden de Llenado</button>
    </div>
</form>
@endsection