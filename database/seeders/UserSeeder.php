<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            1 => 'super_admin',
            2 => 'dueño',
            3 => 'sat',
            4 => 'admin_sucursal',
            5 => 'empleado',
            6 => 'tecnico',
            7 => 'cliente'
        ];

        User::create([
                    'name'      => "Eduardo Admin",
                    'email'     => "eduardo@edynoestudio.com",
                    'password'  => Hash::make('Eduardo1.1'),
                ]); 
        Sucursal::create([
            'nombre' => 'Sucursal Principal',
            'direccion' => 'Calle Principal #123, Ciudad, País',
            'whatsapp' => '123-456-7890',
        ]);
        Sucursal::create([
            'nombre' => 'Sucursal Secundaria',
            'direccion' => 'Avenida Secundaria #456, Ciudad, País',
            'whatsapp' => '987-654-3210',
        ]);
    }
}
