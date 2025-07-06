<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use App\Exports\InventoryMovementsExport;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $type = $request->input('type');
        $query = InventoryMovement::query();

        if ($start && $end) {
            $query->whereBetween('created_at', [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()]);
        }

        $movements = $query->latest()->paginate(15);

        return view('reports.index', compact('movements', 'start', 'end', 'type'));

        if ($type) {
        $query->where('type', $type);
        }   
    }

    public function exportExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $type = $request->input('type');

        return Excel::download(new InventoryMovementsExport($start, $end, $type), 'reporte_movimientos.xlsx');
    }
}
