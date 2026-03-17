<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prenda;
use App\Models\CategoriaPrenda;

class PrendaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // 🟩 TOALLAS
            'Toallas' => [
                ['nombre' => 'Toalla facial', 'tamano' => 'facial', 'unidad' => 'pieza'],
                ['nombre' => 'Toalla de mano', 'tamano' => 'mano', 'unidad' => 'pieza'],
                ['nombre' => 'Toalla de baño', 'tamano' => 'baño', 'unidad' => 'pieza'],
            ],

            // 🟦 SÁBANAS
            'Sábanas' => [
                ['nombre' => 'Juego de sábanas', 'unidad' => 'pieza'],
                ['nombre' => 'Sábana bebé', 'unidad' => 'pieza'],
            ],

            // 🟫 COBIJAS
            'Cobijas' => [
                ['nombre' => 'Cobija', 'unidad' => 'pieza'],
            ],

            // 🟥 EDREDONES
            'Edredones' => [
                ['nombre' => 'Edredón individual', 'tamano' => 'delgado', 'unidad' => 'pieza'],
                ['nombre' => 'Edredón individual', 'tamano' => 'normal',  'unidad' => 'pieza'],
                ['nombre' => 'Edredón individual', 'tamano' => 'jumbo',   'unidad' => 'pieza'],

                ['nombre' => 'Edredón matrimonial', 'tamano' => 'normal', 'unidad' => 'pieza'],
                ['nombre' => 'Edredón matrimonial', 'tamano' => 'jumbo',  'unidad' => 'pieza'],

                ['nombre' => 'Edredón Queen', 'tamano' => 'normal', 'unidad' => 'pieza'],
                ['nombre' => 'Edredón Queen', 'tamano' => 'jumbo',  'unidad' => 'pieza'],

                ['nombre' => 'Edredón King', 'tamano' => 'normal', 'unidad' => 'pieza'],
                ['nombre' => 'Edredón King', 'tamano' => 'jumbo',  'unidad' => 'pieza'],
            ],

            // 🟪 ALMOHADAS
            'Almohadas' => [
                ['nombre' => 'Almohada normal',   'unidad' => 'pieza'],
                ['nombre' => 'Almohada Sognare',  'unidad' => 'pieza'],
            ],

            // 🟨 CUBRECOLCHONES
            'Cubrecolchones' => [
                ['nombre' => 'Cubrecolchón normal',  'unidad' => 'pieza'],
                ['nombre' => 'Cubrecolchón Sognare', 'unidad' => 'pieza'],
            ],

            // 🟧 CHAMARRAS Y ABRIGOS
            'Chamarras y Abrigos' => [
                ['nombre' => 'Chamarra', 'unidad' => 'pieza'],
                ['nombre' => 'Chaleco',  'unidad' => 'pieza'],
            ],

            // 🟫 ROPA INTERIOR
            'Ropa Interior' => [
                ['nombre' => 'Ropa interior por kg', 'unidad' => 'kg'],
            ],

            // 🟦 ACCESORIOS
            'Accesorios' => [
                ['nombre' => 'Gorra',    'unidad' => 'pieza'],
                ['nombre' => 'Mochila',  'unidad' => 'pieza'],
            ],

            // 🟪 MANTELES
            'Manteles' => [
                ['nombre' => 'Manteles 1-4', 'tamano' => '1-4', 'unidad' => 'pieza'],
                ['nombre' => 'Manteles 5-9', 'tamano' => '5-9', 'unidad' => 'pieza'],
            ],

            // 🟨 CALZADO
            'Calzado' => [
                ['nombre' => 'Zapatillas / Tenis', 'unidad' => 'par'],
            ],
        ];

        foreach ($data as $categoriaNombre => $prendas) {

            $categoria = CategoriaPrenda::where('nombre', $categoriaNombre)->first();

            if (!$categoria) {
                $this->command->warn("Categoría no encontrada: $categoriaNombre");
                continue;
            }

            foreach ($prendas as $item) {
                Prenda::firstOrCreate([
                    'categoria_prenda_id' => $categoria->id,
                    'nombre'              => $item['nombre'],
                    'tamano'              => $item['tamano'] ?? null,
                ], [
                    'unidad'       => $item['unidad'] ?? 'pieza',
                    'descripcion'  => $item['descripcion'] ?? null,
                ]);
            }
        }

        $this->command->info('Prendas cargadas correctamente.');
    }
}
