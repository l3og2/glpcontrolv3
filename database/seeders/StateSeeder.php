<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiamos la tabla primero para evitar duplicados si se ejecuta varias veces
        State::truncate();

        $states = [
            ['name' => 'Amazonas', 'code' => 'AMA'],
            ['name' => 'Anzoátegui', 'code' => 'ANZ'],
            ['name' => 'Apure', 'code' => 'APU'],
            ['name' => 'Aragua', 'code' => 'ARA'],
            ['name' => 'Barinas', 'code' => 'BAR'],
            ['name' => 'Bolívar', 'code' => 'BOL'],
            ['name' => 'Carabobo', 'code' => 'CAR'],
            ['name' => 'Cojedes', 'code' => 'COJ'],
            ['name' => 'Delta Amacuro', 'code' => 'DEL'],
            ['name' => 'Distrito Capital', 'code' => 'DCA'],
            ['name' => 'Falcón', 'code' => 'FAL'],
            ['name' => 'Guárico', 'code' => 'GUA'],
            ['name' => 'Lara', 'code' => 'LAR'],
            ['name' => 'Mérida', 'code' => 'MER'],
            ['name' => 'Miranda', 'code' => 'MIR'],
            ['name' => 'Monagas', 'code' => 'MON'],
            ['name' => 'Nueva Esparta', 'code' => 'NUE'],
            ['name' => 'Portuguesa', 'code' => 'POR'],
            ['name' => 'Sucre', 'code' => 'SUC'],
            ['name' => 'Táchira', 'code' => 'TAC'],
            ['name' => 'Trujillo', 'code' => 'TRU'],
            ['name' => 'Vargas (La Guaira)', 'code' => 'VAR'],
            ['name' => 'Yaracuy', 'code' => 'YAR'],
            ['name' => 'Zulia', 'code' => 'ZUL'],
        ];

        // Insertamos los datos en la tabla 'states'
        foreach ($states as $state) {
            State::create($state);
        }    
    }
}
    