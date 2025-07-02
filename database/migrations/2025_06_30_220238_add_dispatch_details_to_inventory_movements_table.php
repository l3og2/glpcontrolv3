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
        Schema::table('inventory_movements', function (Blueprint $table) {
            //Datos del Proveedor y Venta
            $table->string('supply_source')->nullable()->after('notes');
            $table->string('pdvsa_sale_number')->nullable()->after('supply_source');

            // Datos del Chuto (Tractor)
            $table->string('chuto_code')->nullable()->after('pdvsa_sale_number');
            $table->string('chuto_plate')->nullable()->after('chuto_code');
    
            // Datos de la Cisterna
            $table->string('cisterna_code')->nullable()->after('chuto_plate');
            $table->decimal('cisterna_capacity_gallons', 10, 2)->nullable()->after('cisterna_code');
            $table->string('cisterna_plate')->nullable()->after('cisterna_capacity_gallons');
            $table->string('cisterna_serial')->nullable()->after('cisterna_plate');

            // Datos del Conductor
            $table->string('driver_name')->nullable()->after('cisterna_serial');
            $table->string('driver_ci')->nullable()->after('driver_name');
            $table->string('driver_code')->nullable()->after('driver_ci');

            // Lecturas de Tanque de Suministro
            $table->decimal('arrival_volume_percentage', 5, 2)->nullable()->after('driver_code');
            $table->decimal('arrival_temperature', 5, 2)->nullable()->after('arrival_volume_percentage');
            $table->decimal('arrival_pressure', 5, 2)->nullable()->after('arrival_temperature');
            $table->decimal('arrival_specific_gravity', 8, 4)->nullable()->after('arrival_pressure');
    
            $table->decimal('departure_volume_percentage', 5, 2)->nullable()->after('arrival_specific_gravity');
            $table->decimal('departure_temperature', 5, 2)->nullable()->after('departure_volume_percentage');
            $table->decimal('departure_pressure', 5, 2)->nullable()->after('departure_temperature');
            $table->decimal('departure_specific_gravity', 8, 4)->nullable()->after('departure_pressure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            //Es importante eliminar las columnas en un array para que Laravel
            // pueda generar una única consulta de alteración de tabla, lo cual es más eficiente.
            $table->dropColumn([
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
            ]);
        });
    }
};
