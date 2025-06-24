<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tank extends Model
{
    /**
     * Un tanque puede tener muchos movimientos de inventario.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
