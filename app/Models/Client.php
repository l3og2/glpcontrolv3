<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class client extends Model
{
    /**
     * Un cliente puede tener muchos movimientos de inventario.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
