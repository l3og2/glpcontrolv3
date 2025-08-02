<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_closings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que realizó el cierre
            $table->date('closing_date')->unique(); // Solo un cierre por día
            $table->decimal('initial_inventory', 12, 2); // Inventario final del día anterior
            $table->decimal('total_entries', 12, 2); // Total entradas aprobadas del día
            $table->decimal('total_exits', 12, 2); // Total salidas aprobadas del día
            $table->decimal('theorical_inventory', 12, 2); // Inventario teórico (inicial + entradas - salidas)
            $table->decimal('manual_reading', 12, 2); // Lectura manual ingresada por el analista
            $table->decimal('discrepancy', 12, 2); // Diferencia
            $table->decimal('discrepancy_percentage', 5, 2); // Porcentaje de la diferencia
            $table->text('justification')->nullable(); // Justificación si hay discrepancia
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_closings');
    }
};
