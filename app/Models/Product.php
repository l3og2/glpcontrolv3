<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    /** The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'type', 'unit_of_measure', 
        'weight_kg', 'volume_liters',
    ];
 
     /**
     * Un producto puede tener muchos movimientos de inventario.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
