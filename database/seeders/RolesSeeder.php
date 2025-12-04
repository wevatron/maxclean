<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'root',
            'dueño',
            'sat',
            'admin_sucursal',
            'empleado',
            'tecnico',
            'cliente',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $this->command->info('🎉 Roles creados o verificados correctamente.');
    }
}
