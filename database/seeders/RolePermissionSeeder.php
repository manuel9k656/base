<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos de usuarios
        $permissions = [
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'eliminar-usuarios',
            'asignar-roles',
            'ver-roles',
            'crear-roles',
            'editar-roles',
            'eliminar-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::create(['name' => 'Administrador']);
        $userRole = Role::create(['name' => 'Usuario']);
        $supervisorRole = Role::create(['name' => 'Supervisor']);

        // Asignar todos los permisos al Administrador
        $adminRole->givePermissionTo(Permission::all());

        // Asignar permisos limitados al Supervisor
        $supervisorRole->givePermissionTo([
            'ver-usuarios',
            'editar-usuarios',
            'ver-roles',
        ]);

        // Asignar permisos básicos al Usuario
        $userRole->givePermissionTo(['ver-usuarios']);

        // Asignar rol Administrador al primer usuario
        $admin = User::where('email', 'admin@yopmail.com')->first();
        if ($admin) {
            $admin->assignRole('Administrador');
        }

        // Asignar rol Usuario al segundo usuario
        $user = User::where('email', 'manuelmtz9k@gmail.com')->first();
        if ($user) {
            $user->assignRole('Usuario');
        }
    }
}