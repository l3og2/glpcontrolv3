<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
 
    use HasFactory;

     
    /** The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'state_id',
        'control_number',
        'status',
        'type',
        'movement_date',
        'volume_liters',
        'tank_id',
        'product_id',
        'client_id',
        'notes',
        'supply_source',
        'pdvsa_sale_number',
        'chuto_code',
        'chuto_plate',
        'cisterna_code',
        'cisterna_capacity_gallons',
        'cisterna_plate',
        'cisterna_serial',
        'driver_name',
        'driver_ci',
        'driver_code',
        'arrival_volume_percentage',
        'arrival_temperature',
        'arrival_pressure',
        'arrival_specific_gravity',
        'departure_volume_percentage',
        'departure_temperature',
        'departure_pressure',
        'departure_specific_gravity',
    ];

    /**
     * The attributes that should be cast.
     * Esto es una buena práctica para que Laravel trate la columna
     * 'movement_date' siempre como un objeto de fecha.
     *
     * @var array
     */
    protected $casts = [
        'movement_date' => 'datetime',
    ];

    /**
        * Un movimiento pertenece a un usuario, un estado, un producto, etc.
     */
    public function user() { return $this->belongsTo(User::class); }
    public function state() { return $this->belongsTo(State::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function tank() { return $this->belongsTo(Tank::class); }
    public function client() { return $this->belongsTo(Client::class); }
}
