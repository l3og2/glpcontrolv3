<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llama a nuestro nuevo seeder
    $this->call([
        StateSeeder::class,
        ProductSeeder::class,
        TankSeeder::class,
        RolesAndPermissionsSeeder::class,
        PriceSeeder::class,
        // Aquí puedes añadir otros seeders en el futuro
    ]);

    // Opcional: Crear un usuario Admin por defecto
    \App\Models\User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@gascomunal.com.ve',
        'state_id' => null,
    ])->assignRole('Admin');
    
    }
}
