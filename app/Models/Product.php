<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    /**
     * Un producto puede tener muchos movimientos de inventario.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
