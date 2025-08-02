<?php

namespace App\Services;

use App\Models\{InventoryMovement, DailyClosing};
use Carbon\Carbon;

class InventoryService
{
    public function getDailyClosingData(int $stateId, Carbon $date)
    {
        // 1. Buscar el inventario final del último cierre registrado para este estado
        $lastClosing = DailyClosing::where('state_id', $stateId)
                                   ->orderBy('closing_date', 'desc')
                                   ->first();
        
        $initialInventory = $lastClosing ? $lastClosing->theorical_inventory : 0;

        // 2. Sumar todas las entradas APROBADAS del día
        $totalEntries = InventoryMovement::where('state_id', $stateId)
            ->where('type', 'entrada')->where('status', 'aprobado')
            ->whereDate('movement_date', $date)->sum('volume_liters');
            
        // 3. Restar todas las salidas APROBADAS del día
        $totalExits = InventoryMovement::where('state_id', $stateId)
            ->where('type', 'salida')->where('status', 'aprobado')
            ->whereDate('movement_date', $date)->sum('volume_liters');
            
        $theoricalInventory = $initialInventory + $totalEntries - $totalExits;

        return [
            'initial_inventory' => $initialInventory,
            'total_entries' => $totalEntries,
            'total_exits' => $totalExits,
            'theorical_inventory' => $theoricalInventory,
        ];
    }
}