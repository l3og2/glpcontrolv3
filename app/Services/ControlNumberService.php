<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\State;
use Carbon\Carbon;

class ControlNumberService
{
    public function generateForState(int $stateId, Carbon $date = null): string
{
    $state = State::findOrFail($stateId);
    $targetDate = $date ? $date->copy()->startOfDay() : Carbon::today();
    
    $prefix = $state->code . '-' . $targetDate->format('ymd');

    // --- INICIO DE LA CORRECCIÓN ---
    // En lugar de buscar por 'created_at', buscamos por 'movement_date',
    // que es la fecha de la operación que estamos registrando.
    $lastMovement = InventoryMovement::where('state_id', $stateId)
        ->whereDate('movement_date', $targetDate) // <-- CAMBIO CLAVE
        ->orderBy('control_number', 'desc') // Ordenamos por el propio número de control para mayor precisión
        ->first();
    // --- FIN DE LA CORRECCIÓN ---

    $sequence = 1;
    if ($lastMovement) {
        // La lógica para extraer el secuencial sigue siendo la misma y es correcta.
        $lastSequence = (int) substr($lastMovement->control_number, -3);
        $sequence = $lastSequence + 1;
    }

    $formattedSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);

    return $prefix . '-' . $formattedSequence;
}
}
