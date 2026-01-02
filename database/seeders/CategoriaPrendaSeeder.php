<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaPrenda;

class CategoriaPrendaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Toallas',
            'Sábanas',
            'Cobijas',
            'Edredones',
            'Almohadas',
            'Cubrecamas',
            'Chamarras',
            'Ropa interior',
            'Varios',
        ];

        foreach ($categorias as $nombre) {
            CategoriaPrenda::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
