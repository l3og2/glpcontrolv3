<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        // --- 1. Lógica de Filtrado por Rol ---
        $query = InventoryMovement::query();
        if ($user->hasRole('Gerente Regional')) {
            $query->where('state_id', $user->state_id);
        }
        // Para el Admin, el query no tiene filtro de estado, por lo que verá todo el país.
        
        // --- 2. Cálculo de KPIs (Tarjetas) ---
        $kpis = [
            'total_sales' => (clone $query)->where('type', 'salida')->where('status', 'aprobado')->whereMonth('movement_date', $now->month)->whereYear('movement_date', $now->year)->sum('total_amount'),
            'volume_dispatch' => (clone $query)->where('type', 'salida')->where('status', 'aprobado')->whereMonth('movement_date', $now->month)->whereYear('movement_date', $now->year)->sum('volume_liters'),
            'volume_entry' => (clone $query)->where('type', 'entrada')->where('status', 'aprobado')->whereMonth('movement_date', $now->month)->whereYear('movement_date', $now->year)->sum('volume_liters'),
            'pending_movements' => (clone $query)->whereIn('status', ['ingresado', 'revisado'])->count(),
        ];
        
        // --- 3. Datos para Gráfico de Tendencias (Últimos 6 meses) ---
        $trendData = (clone $query)
            ->where('movement_date', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->where('status', 'aprobado')
            ->select(
                DB::raw("TO_CHAR(movement_date, 'YYYY-MM') as month"),
                DB::raw("SUM(CASE WHEN type = 'entrada' THEN volume_liters ELSE 0 END) as total_entradas"),
                DB::raw("SUM(CASE WHEN type = 'salida' THEN volume_liters ELSE 0 END) as total_salidas")
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
            
        // Formateamos los datos para Chart.js
        $chartLabels = $trendData->map(fn($item) => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'));
        $chartDataEntradas = $trendData->pluck('total_entradas');
        $chartDataSalidas = $trendData->pluck('total_salidas');

        // --- 4. Datos para Tabla de Productos Más Vendidos (Mes Actual) ---
        $topProducts = (clone $query)
            ->where('type', 'salida')
            ->where('status', 'aprobado')
            ->whereMonth('movement_date', $now->month)
            ->whereYear('movement_date', $now->year)
            ->with('product') // Carga la relación con el producto
            ->select('product_id', DB::raw('SUM(volume_liters) as total_volume'))
            ->groupBy('product_id')
            ->orderBy('total_volume', 'desc')
            ->limit(5)
            ->get();
            
        return view('dashboard', compact('kpis', 'chartLabels', 'chartDataEntradas', 'chartDataSalidas', 'topProducts'));
    }
}