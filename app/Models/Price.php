<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    /**
     * Indica que el modelo no tiene una clave primaria auto-incremental.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * La clave primaria asociada con la tabla.
     * En este caso, es una clave compuesta, pero para Eloquent,
     * definimos una de ellas (o la dejamos nula si no se necesita para find()).
     * Al ponerla nula, y con $incrementing = false, Eloquent ya no buscará un 'id'.
     *
     * @var string
     */
    protected $primaryKey = null;

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