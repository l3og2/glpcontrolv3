@extends('layouts.app')
@section('title', 'Historial de Cierres Diarios')

@section('header-actions')
    @can('perform daily closing')
        <a href="{{ route('daily-closing.create') }}" class="btn btn-primary">
            <i class="bi bi-calendar-plus-fill me-1"></i> Realizar Cierre de Hoy
        </a>
    @endcan
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha de Cierre</th>
                        <th>Estado</th>
                        <th class="text-end">Inv. Teórico</th>
                        <th class="text-end">Lectura Manual</th>
                        <th class="text-end">Discrepancia (%)</th>
                        <th>Realizado Por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($closings as $closing)
                        <tr>
                            <td><strong>{{ $closing->closing_date->format('d/m/Y') }}</strong></td>
                            <td>{{ $closing->state->name }}</td>
                            <td class="text-end">{{ number_format($closing->theorical_inventory, 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($closing->manual_reading, 2, ',', '.') }}</td>
                            <td class="text-end {{ $closing->discrepancy_percentage > 3 ? 'text-danger fw-bold' : 'text-success' }}">
                                {{ number_format($closing->discrepancy_percentage, 2, ',', '.') }}%
                            </td>
                            <td>{{ $closing->user->name }}</td>
                            <td>
                                <a href="{{ route('daily-closing.show', $closing) }}" class="btn btn-sm btn-outline-primary">Ver Detalle</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No hay cierres registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($closings->hasPages())
            <div class="d-flex justify-content-end mt-3">{{ $closings->links() }}</div>
        @endif
    </div>
</div>
@endsection