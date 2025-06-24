<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\State;
use Carbon\Carbon;

class ControlNumberService
{
    public function generateForState(int $stateId): string
    {
        $state = State::findOrFail($stateId);
        $today = Carbon::today();
        $prefix = $state->code . '-' . $today->format('ymd'); // Ej: MIR-240622

        $lastMovement = InventoryMovement::where('state_id', $stateId)
            ->whereDate('created_at', $today)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastMovement) {
            // Extraemos el último número secuencial y le sumamos 1
            $lastSequence = (int) substr($lastMovement->control_number, -3);
            $sequence = $lastSequence + 1;
        }

        // Formateamos el secuencial a 3 dígitos con ceros a la izquierda (ej: 001, 015, 123)
        $formattedSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);

        return $prefix . '-' . $formattedSequence;
    }
}
