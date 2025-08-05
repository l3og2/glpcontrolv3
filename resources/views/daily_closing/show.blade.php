@extends('layouts.app')
@section('title', 'Detalle de Cierre Diario')

@section('header-actions')
    <a href="{{ route('daily-closing.index') }}" class="btn btn-secondary">← Volver al Historial</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        Detalle del Cierre del Día: <strong>{{ $dailyClosing->closing_date->format('d/m/Y') }}</strong> para el estado de <strong>{{ $dailyClosing->state->name }}</strong>
    </div>
    <div class="card-body">
        <!-- Aquí iría la lógica futura de aprobación por parte del Gerente -->
        <div class="row">
            <div class="col-md-4"><strong>Inventario Final Ayer:</strong><p class="fs-5">{{ number_format($dailyClosing->initial_inventory, 2, ',', '.') }} Lts</p></div>
            <div class="col-md-4"><strong>Entradas Aprobadas:</strong><p class="fs-5 text-success">+ {{ number_format($dailyClosing->total_entries, 2, ',', '.') }} Lts</p></div>
            <div class="col-md-4"><strong>Salidas Aprobadas:</strong><p class="fs-5 text-danger">- {{ number_format($dailyClosing->total_exits, 2, ',', '.') }} Lts</p></div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-4"><strong>Inventario Teórico:</strong><p class="fs-5 fw-bold">{{ number_format($dailyClosing->theorical_inventory, 2, ',', '.') }} Lts</p></div>
            <div class="col-md-4"><strong>Lectura Manual:</strong><p class="fs-5 fw-bold">{{ number_format($dailyClosing->manual_reading, 2, ',', '.') }} Lts</p></div>
            <div class="col-md-4"><strong>Discrepancia:</strong><p class="fs-5 fw-bold {{ $dailyClosing->discrepancy_percentage > 3 ? 'text-danger' : 'text-success' }}">{{ number_format($dailyClosing->discrepancy, 2, ',', '.') }} Lts ({{ number_format($dailyClosing->discrepancy_percentage, 2, ',', '.') }}%)</p></div>
        </div>
        @if($dailyClosing->justification)
        <div class="mt-3">
            <strong>Justificación:</strong>
            <div class="card bg-light mt-1"><div class="card-body">{{ $dailyClosing->justification }}</div></div>
        </div>
        @endif
    </div>
    <div class="card-footer text-muted small">
        Cierre realizado por {{ $dailyClosing->user->name }} el {{ $dailyClosing->created_at->format('d/m/Y H:i A') }}
    </div>
</div>
@endsection