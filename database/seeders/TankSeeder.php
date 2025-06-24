<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Tank;

class TankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tank::truncate(); // Limpia la tabla para evitar duplicados

        // Buscamos el estado de Miranda para asociar los tanques
        $mirandaState = State::where('name', 'Miranda')->first();

        // Solo creamos los tanques si encontramos el estado de Miranda
        if ($mirandaState) {
            $tanks = [
                // Lista 1
                ['name_location' => 'Planta Jefa Apacuana', 'capacity_gallons' => 50000],
                ['name_location' => 'Planta General José Felix Ribas', 'capacity_gallons' => 75000],
                ['name_location' => 'Planta Cacique Guaicaipuro', 'capacity_gallons' => 60000],
                ['name_location' => 'Planta Cacique Baruta', 'capacity_gallons' => 45000],
                ['name_location' => 'Planta Warairarepano', 'capacity_gallons' => 80000],
                ['name_location' => 'Planta Hector Rivas', 'capacity_gallons' => 30000],
                ['name_location' => 'Planta Automatizada', 'capacity_gallons' => 100000],
                ['name_location' => 'Planta Miguel Acevedo', 'capacity_gallons' => 55000],              
            ];

            foreach ($tanks as $tank) {
                // Creamos cada tanque y le asociamos el ID del estado Miranda
                Tank::create([
                    'state_id' => $mirandaState->id,
                    'name_location' => $tank['name_location'],
                    'capacity_gallons' => $tank['capacity_gallons'],
                ]);
            }
        }
        
        // Aquí podrías añadir lógica similar para otros estados...
        // $araguaState = State::where('name', 'Aragua')->first();
        // if ($araguaState) { ... }
    }
}