<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia la caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- CREACIÓN DE PERMISOS ---
        // Permisos para Usuarios
        Permission::firstOrCreate(['name' => 'manage users']); // CRUD completo de usuarios

        // Permisos para Movimientos de Inventario
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'create movements']);
        Permission::firstOrCreate(['name' => 'review movements']);
        Permission::firstOrCreate(['name' => 'approve movements']);
        Permission::firstOrCreate(['name' => 'view reports']);

        // Permisos para Reportes
        Permission::firstOrcreate(['name' => 'view reports']);

        // --- CREACIÓN DE ROLES Y ASIGNACIÓN DE PERMISOS ---
        
        // Rol Analista: Solo puede crear movimientos
        $analystRole = Role::firstOrCreate(['name' => 'Analista']);
        $analystRole->givePermissionTo('create movements');

        // Rol Supervisor: Puede crear y revisar
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);
        $supervisorRole->givePermissionTo(['create movements', 'review movements']);

        // Rol Gerente Regional: Puede hacer todo lo de un supervisor + aprobar y ver reportes
        $managerRole = Role::firstOrCreate(['name' => 'Gerente Regional']);
        $managerRole->givePermissionTo(['create movements', 'review movements', 'approve movements', 'view reports']);
        
        // Rol Admin: Tiene todos los permisos, incluyendo gestionar usuarios.
        // Es una buena práctica no asignar permisos uno por uno, sino darle acceso total.
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}