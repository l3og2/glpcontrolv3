<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    /**
     * Un Estado puede tener muchos movimientos de inventario.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
