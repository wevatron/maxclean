<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            1 => 'root',
            2 => 'administracion',
            3 => 'administracion_secundaria',
            4 => 'administracion_preparatoria',
            5 => 'maestro',
            6 => 'alumno',
            7 => 'padre',
            8 => 'maestro_padre',
            9 => 'control_escolar_admin',
            10 => 'control_escolar_secu',
            11 => 'control_escolar_prepa',
            12 => 'sin_identificar',
        ];

        foreach ($roles as $rolId => $nombreRol) {
            for ($i = 1; $i <= 10; $i++) {
                User::create([
                    'name'      => ucfirst($nombreRol) . " $i",
                    'email'     => "{$nombreRol}{$i}@example.com",
                    'password'  => Hash::make('password123'),
                ]);
            }
        }
    }
}
