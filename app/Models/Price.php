<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'state_id',
        'price',
    ];

    /**
     * Define la relación de que un Precio pertenece a un Producto.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Define la relación de que un Precio pertenece a un Estado.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}