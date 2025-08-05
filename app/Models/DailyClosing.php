<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyClosing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'state_id',
        'user_id',
        'closing_date',
        'initial_inventory',
        'total_entries',
        'total_exits',
        'theorical_inventory',
        'manual_reading',
        'discrepancy',
        'discrepancy_percentage',
        'justification',
        
        // --- Campos de Auditoría (si los tienes en la BD) ---
        'remarks',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'closing_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES DE ELOQUENT
    |--------------------------------------------------------------------------
    */

    /**
     * Define la relación: Un cierre diario pertenece a un Estado.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Define la relación: Un cierre diario fue creado por un Usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define la relación: Un cierre diario fue aprobado por un Usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}