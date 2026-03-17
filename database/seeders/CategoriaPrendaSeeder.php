<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaPrenda;

class CategoriaPrendaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Toallas',
                'descripcion' => 'Prendas absorbentes como toallas de mano, baño y faciales.',
            ],
            [
                'nombre' => 'Sábanas',
                'descripcion' => 'Piezas de cama como sábanas individuales, matrimoniales y juegos completos.',
            ],
            [
                'nombre' => 'Cobijas',
                'descripcion' => 'Cobertores ligeros y prendas similares.',
            ],
            [
                'nombre' => 'Edredones',
                'descripcion' => 'Edredones de todo tipo: individual, matrimonial, queen y king.',
            ],
            [
                'nombre' => 'Almohadas',
                'descripcion' => 'Almohadas normales y de marcas especiales.',
            ],
            [
                'nombre' => 'Cubrecolchones',
                'descripcion' => 'Protectores de colchón de diversos materiales.',
            ],
            [
                'nombre' => 'Chamarras y Abrigos',
                'descripcion' => 'Prendas exteriores como chamarras, chalecos y abrigos.',
            ],
            [
                'nombre' => 'Ropa Interior',
                'descripcion' => 'Ropa interior que se cobra por kilogramo.',
            ],
            [
                'nombre' => 'Accesorios',
                'descripcion' => 'Artículos como gorras, mochilas y otros accesorios.',
            ],
            [
                'nombre' => 'Manteles',
                'descripcion' => 'Manteles de distintos tamaños y materiales.',
            ],
            [
                'nombre' => 'Calzado',
                'descripcion' => 'Artículos como zapatillas y tenis.',
            ],
        ];

        foreach ($categorias as $data) {
            CategoriaPrenda::firstOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }

        $this->command->info('Categorías de prendas creadas correctamente.');
    }
}
