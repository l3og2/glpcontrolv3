<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\DailyClosing;
use App\Services\InventoryService;

class DailyClosingController extends Controller
{
    /**
     * Muestra una lista paginada del historial de cierres diarios.
     */
    public function index()
    {
        $query = DailyClosing::with('user', 'state');
        $user = Auth::user();

        // Los usuarios no-administradores solo ven los cierres de su propio estado.
        // Los administradores ven todos.
        if (!$user->hasRole('Admin')) {
            $query->where('state_id', $user->state_id);
        }
        
        $closings = $query->orderBy('closing_date', 'desc')->paginate(15);
        
        return view('daily_closing.index', compact('closings'));
    }

    /**
     * Muestra el formulario para crear un nuevo cierre diario.
     */
    public function create(InventoryService $inventoryService)
    {
        $today = Carbon::today();
        $user = Auth::user();

        // Verificamos si ya se hizo un cierre para el día de hoy en este estado.
        if (DailyClosing::where('state_id', $user->state_id)->whereDate('closing_date', $today)->exists()) {
            return redirect()->route('daily-closing.index')
                ->with('warning', 'El cierre para el día de hoy ya fue realizado.');
        }

        $closingData = $inventoryService->getDailyClosingData($user->state_id, $today);
        $categories = ['Residencial', 'Comercial', 'Convenios'];

            foreach ($categories as $category) {

            if (!isset($closingData['all_products'][$category])) {
            $closingData['all_products'][$category] = collect();
            }

            if (!isset($closingData['sales_summary'][$category])) {
            $closingData['sales_summary'][$category] = collect();
            }
        }       

        return view('daily_closing.create', compact('closingData', 'today'));
    }

    /**
     * Guarda un nuevo cierre diario en la base de datos.
     */
    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'manual_reading' => 'required|numeric|min:0',
            'justification' => 'nullable|string|max:1000',
        ]);
        
        $today = Carbon::today();
        $user = Auth::user();

        // Prevenimos cierres duplicados por si el usuario abre dos pestañas
        if (DailyClosing::where('state_id', $user->state_id)->whereDate('closing_date', $today)->exists()) {
            return redirect()->route('daily-closing.index')
                ->with('warning', 'El cierre para el día de hoy ya fue realizado por otro usuario.');
        }

        $closingData = $inventoryService->getDailyClosingData($user->state_id, $today);
        
        // El valor de la lectura manual viene como un string, lo convertimos a float.
        $manualReading = (float) str_replace(',', '.', str_replace('.', '', $request->manual_reading));
        $theoricalInventory = $closingData['theorical_inventory'];
        $discrepancy = $manualReading - $theoricalInventory;
        
        // Evitamos la división por cero si el inventario teórico es 0
        $discrepancyPercentage = $theoricalInventory != 0 ? abs(($discrepancy / $theoricalInventory) * 100) : ($manualReading > 0 ? 100 : 0);
        
        // Validamos la discrepancia en el backend como segunda capa de seguridad
        if ($discrepancyPercentage > 1) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['manual_reading' => 'La discrepancia no puede superar el 1%. Por favor, revise los movimientos o justifique la diferencia.']);
        }
        
        DailyClosing::create([
            'state_id' => $user->state_id,
            'user_id' => $user->id,
            'closing_date' => $today,
            'initial_inventory' => $closingData['initial_inventory'],
            'total_entries' => $closingData['total_entries'],
            'total_exits' => $closingData['total_exits'],
            'theorical_inventory' => $theoricalInventory,
            'manual_reading' => $manualReading,
            'discrepancy' => $discrepancy,
            'discrepancy_percentage' => $discrepancyPercentage,
            'justification' => $request->justification,
        ]);
        
        return redirect()->route('daily-closing.index')->with('success', 'Cierre del día realizado con éxito.');
    }

    /**
     * Muestra el detalle de un cierre diario específico.
     */
    public function show(DailyClosing $dailyClosing)
    {
        $user = Auth::user();
        
        // Lógica de autorización: el usuario solo puede ver cierres de su estado (a menos que sea Admin).
        if (!$user->hasRole('Admin') && $user->state_id !== $dailyClosing->state_id) {
            abort(403, 'Acción no autorizada.');
        }

        return view('daily_closing.show', compact('dailyClosing'));
    }
}