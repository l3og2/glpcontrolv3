<?php

namespace App\Exports;

use App\Models\InventoryMovement;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class InventoryMovementsExport implements FromView
{
    protected $filters;

    // Recibimos un array con todos los filtros
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        // Reutilizamos la misma lógica de consulta que en ReportController@generate
        $query = InventoryMovement::with('user', 'product', 'tank', 'state');

        // Aplicamos los filtros
        $query->whereBetween('movement_date', [$this->filters['start_date'], $this->filters['end_date']]);

        if (Auth::user()->hasRole('Admin') && !empty($this->filters['state_id'])) {
            $query->where('state_id', $this->filters['state_id']);
        } else if (!Auth::user()->hasRole('Admin')) {
            $query->where('state_id', Auth::user()->state_id);
        }
        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }
        if (!empty($this->filters['product_id'])) {
            $query->where('product_id', $this->filters['product_id']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Obtenemos TODOS los resultados, sin paginar
        $movements = $query->orderBy('movement_date', 'desc')->get();

        // Pasamos la colección de movimientos a la vista de Excel
        return view('reports.excel', [
            'movements' => $movements
        ]);
    }
}