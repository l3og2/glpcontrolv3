<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia la caché de roles y permisos para evitar inconsistencias
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- 1. CREAMOS TODOS LOS PERMISOS PRIMERO (SIN DUPLICADOS) ---
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'create movements']);
        Permission::firstOrCreate(['name' => 'review movements']);
        Permission::firstOrCreate(['name' => 'approve movements']);
        Permission::firstOrCreate(['name' => 'view reports']);
        Permission::firstOrCreate(['name' => 'perform daily closing']);

        // --- 2. CREAMOS ROLES Y SINCRONIZAMOS SUS PERMISOS ---
        
        // Rol Analista: Solo puede crear movimientos
        $analystRole = Role::firstOrCreate(['name' => 'Analista']);
        // Usamos syncPermissions para asegurar que TENGA ESTE y SOLO ESTE permiso
        $analystRole->syncPermissions(['create movements', 'perform daily closing']);

        // Rol Supervisor: Puede crear y revisar
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);
        $supervisorRole->syncPermissions(['create movements', 'review movements', 'perform daily closing']);

        // Rol Gerente Regional: Puede hacer casi todo, excepto gestionar usuarios
        $managerRole = Role::firstOrCreate(['name' => 'Gerente Regional']);
        $managerRole->syncPermissions([
            'create movements', 
            'review movements', 
            'approve movements', 
            'view reports'
        ]);
        
        // Rol Admin: Tiene todos los permisos
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        $this->command->info('Roles y permisos sincronizados correctamente.');
    }
}