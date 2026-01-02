<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaPrenda;
use App\Models\Prenda;

class PrendaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // TOALLAS
            'Toallas' => [
                ['nombre' => 'Toalla facial'],
                ['nombre' => 'Toalla de mano'],
                ['nombre' => 'Toalla de baño'],
            ],

            // SÁBANAS
            'Sábanas' => [
                ['nombre' => 'Juego de sábanas'],
                ['nombre' => 'Sábana bebé'],
            ],

            // COBIJAS
            'Cobijas' => [
                ['nombre' => 'Cobija'],
            ],

            // EDREDONES
            'Edredones' => [
                ['nombre' => 'Edredón individual delgado', 'tamano' => 'delgado'],
                ['nombre' => 'Edredón individual normal',  'tamano' => 'normal'],
                ['nombre' => 'Edredón individual jumbo',   'tamano' => 'jumbo'],

                ['nombre' => 'Edredón matrimonial normal', 'tamano' => 'normal'],
                ['nombre' => 'Edredón matrimonial jumbo',  'tamano' => 'jumbo'],

                ['nombre' => 'Edredón Queen normal',       'tamano' => 'normal'],
                ['nombre' => 'Edredón Queen jumbo',        'tamano' => 'jumbo'],

                ['nombre' => 'Edredón King normal',        'tamano' => 'normal'],
                ['nombre' => 'Edredón King jumbo',         'tamano' => 'jumbo'],
            ],

            // ALMOHADAS
            'Almohadas' => [
                ['nombre' => 'Almohada normal'],
                ['nombre' => 'Almohada Sognare'],
            ],

            // CUBRECAMAS
            'Cubrecamas' => [
                ['nombre' => 'Cubrecolchón normal'],
                ['nombre' => 'Cubrecolchón Sognare'],
            ],

            // CHAMARRAS
            'Chamarras' => [
                ['nombre' => 'Chamarra'],
                ['nombre' => 'Chaleco'],
            ],

            // ROPA INTERIOR
            'Ropa interior' => [
                ['nombre' => 'Ropa interior por kg', 'unidad' => 'kg'],
            ],

            // VARIOS
            'Varios' => [
                ['nombre' => 'Gorra'],
                ['nombre' => 'Mochila'],
                ['nombre' => 'Manteles 1-4'],
                ['nombre' => 'Manteles 5-9'],
                ['nombre' => 'Zapatillas / Tenis'],
            ],
        ];

        foreach ($data as $categoria => $prendas) {
            $cat = CategoriaPrenda::where('nombre', $categoria)->first();

            foreach ($prendas as $item) {
                Prenda::firstOrCreate([
                    'categoria_prenda_id' => $cat->id,
                    'nombre' => $item['nombre'],
                ], [
                    'tamano' => $item['tamano'] ?? null,
                    'unidad' => $item['unidad'] ?? 'pieza',
                ]);
            }
        }
    }
}
