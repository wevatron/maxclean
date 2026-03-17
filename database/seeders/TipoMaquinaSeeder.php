<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoMaquina;

class TipoMaquinaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nombre'        => 'Lavadora chica',
                'descripcion'   => 'Lavadora de capacidad pequeña, ideal para cargas ligeras.',
                'capacidad_kg'  => 10,
                'tiempo_minimo' => 25,
                'tiempo_maximo' => 35,
            ],
            [
                'nombre'        => 'Lavadora mediana',
                'descripcion'   => 'Lavadora de capacidad media, adecuada para cargas estándar.',
                'capacidad_kg'  => 15,
                'tiempo_minimo' => 30,
                'tiempo_maximo' => 45,
            ],
            [
                'nombre'        => 'Lavadora grande',
                'descripcion'   => 'Lavadora para cargas grandes o artículos voluminosos.',
                'capacidad_kg'  => 20,
                'tiempo_minimo' => 35,
                'tiempo_maximo' => 55,
            ],
            [
                'nombre'        => 'Secadora chica',
                'descripcion'   => 'Secadora pequeña, adecuada para pocas prendas.',
                'capacidad_kg'  => 10,
                'tiempo_minimo' => 25,
                'tiempo_maximo' => 40,
            ],
            [
                'nombre'        => 'Secadora mediana',
                'descripcion'   => 'Secadora de capacidad media, ideal para cargas estándar.',
                'capacidad_kg'  => 15,
                'tiempo_minimo' => 30,
                'tiempo_maximo' => 45,
            ],
            [
                'nombre'        => 'Secadora grande',
                'descripcion'   => 'Secadora de gran capacidad para cargas pesadas o voluminosas.',
                'capacidad_kg'  => 20,
                'tiempo_minimo' => 35,
                'tiempo_maximo' => 55,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoMaquina::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }

        $this->command->info('Seeder de tipos de máquinas ejecutado correctamente.');
    }
}
