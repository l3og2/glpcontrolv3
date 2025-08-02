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
        
        // --- Auditoría y Aprobación ---
        'remarks',                // Observaciones o notas del operador.
        'approved_by',            // El ID del usuario (Supervisor/Gerente) que aprueba/rechaza.
        'approved_at',            // La fecha y hora de la aprobación/rechazo.
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'closing_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who created the daily closing.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who approved the daily closing.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}