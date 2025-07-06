<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'rif',
        'state_id',
        'type',
    ];
        
    /**
     * Un cliente puede tener muchos movimientos de inventario.
     */
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
