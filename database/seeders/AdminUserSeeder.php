<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Creamos el usuario Administrador y le asignamos el rol 'Admin'
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gascomunal.com.ve',
            'state_id' => null, // El admin no pertenece a un estado específico
        ]);
        
        $adminUser->assignRole('Admin');
    }
}