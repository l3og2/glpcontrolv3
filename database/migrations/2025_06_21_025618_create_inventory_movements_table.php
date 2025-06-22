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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->string('control_number')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('state_id')->constrained();
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained();
            $table->foreignId('tank_id')->nullable()->constrained();
            $table->enum('type', ['entrada', 'salida']);
            $table->enum('status', ['ingresado', 'revisado', 'aprobado'])->default('ingresado');
            $table->decimal('volume_liters', 12, 2);
            $table->timestamp('movement_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
