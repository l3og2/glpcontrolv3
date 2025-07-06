<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Este seeder carga TODOS los datos esenciales para que la app funcione.
     */
    public function run(): void
    {
        $this->command->info('Ejecutando seeders de datos maestros...');
        
        // El orden es importante para las relaciones de la base de datos
        $this->call([
            StateSeeder::class,               // 1. Estados
            ProductSeeder::class,             // 2. Productos
            TankSeeder::class,                // 3. Tanques (depende de estados)
            RolesAndPermissionsSeeder::class, // 4. Roles y Permisos
            PriceSeeder::class,               // 5. Precios (depende de productos y estados)
            AdminUserSeeder::class,           // 6. Usuario Administrador (depende de roles)
        ]);

        $this->command->info('¡Seeders de datos maestros completados!');

        // NOTA: Hemos eliminado la llamada a FakeDataSeeder de aquí.
    }
}