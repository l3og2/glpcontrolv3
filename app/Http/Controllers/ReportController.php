<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use App\Exports\InventoryMovementsExport;
use App\Models\State;
use App\Models\Product;
use Spatie\Permission\Traits\HasRoles;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    use HasRoles;

    public function index(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $type = $request->input('type');
        $query = InventoryMovement::query();
        $states = State::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        if ($start && $end) {
            $query->whereBetween('created_at', [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()]);
        }
        
        if ($type) {
        $query->where('type', $type);
        }   

        $movements = $query->latest()->paginate(15);
        return view('reports.index', compact('movements', 'start', 'end', 'type', 'states', 'products'));        
    }

    public function generate(Request $request)
    {
        // 1. Validamos que las fechas existan
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // 2. Iniciamos la consulta de Eloquent
        $query = InventoryMovement::with('product', 'tank', 'state');

        // 3. Aplicamos los filtros de forma dinámica
        
        // Filtro por rango de fechas (obligatorio)
        $query->whereBetween('movement_date', [$request->start_date, $request->end_date]);

        // Filtro por estado (si el usuario es Admin y seleccionó uno)
        if (Auth::user()->hasRole('Admin') && $request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        } else {
            // Los otros roles solo ven los de su propio estado
            $query->where('state_id', Auth::user()->state_id);
        }

        // Filtro por tipo de movimiento (entrada/salida)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por producto
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        // Filtro por estado del movimiento
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Ejecutamos la consulta y paginamos los resultados
        $movements = $query->orderBy('movement_date', 'desc')->paginate(25);
        $movements->withQueryString();

        // 5. Devolvemos la vista de resultados, pasando los movimientos y los filtros originales
        return view('reports.results', [
            'movements' => $movements, 
            'filters' => $request->all(),
        ]);
    }    
    
    /**
     * Exporta los movimientos de inventario a un archivo Excel.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */    
    public function exportExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $type = $request->input('type');

        return Excel::download(new InventoryMovementsExport($start, $end, $type), 'reporte_movimientos.xlsx');
    }
}
