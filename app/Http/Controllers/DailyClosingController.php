<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\DailyClosing;
use App\Services\InventoryService;

class DailyClosingController extends Controller
{
    public function create(InventoryService $inventoryService)
    {
        $today = Carbon::today();
        $user = Auth::user();

        // Verificamos si ya se hizo un cierre para hoy
        if (DailyClosing::where('state_id', $user->state_id)->whereDate('closing_date', $today)->exists()) {
            return redirect()->route('dashboard')->with('warning', 'El cierre para el día de hoy ya fue realizado.');
        }

        $closingData = $inventoryService->getDailyClosingData($user->state_id, $today);

        return view('daily_closing.create', compact('closingData', 'today'));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'manual_reading' => 'required|numeric|min:0',
            'justification' => 'nullable|string',
        ]);
        
        $today = Carbon::today();
        $user = Auth::user();
        $closingData = $inventoryService->getDailyClosingData($user->state_id, $today);
        
        $manualReading = (float) $request->manual_reading;
        $theoricalInventory = $closingData['theorical_inventory'];
        $discrepancy = $manualReading - $theoricalInventory;
        // Evitamos división por cero si el inventario teórico es 0
        $discrepancyPercentage = $theoricalInventory != 0 ? abs(($discrepancy / $theoricalInventory) * 100) : 0;
        
        if ($discrepancyPercentage > 3) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['manual_reading' => 'La discrepancia no puede superar el 3%. Por favor, revise los movimientos o justifique la diferencia.']);
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
        
        return redirect()->route('dashboard')->with('success', 'Cierre del día realizado con éxito.');
    }
}