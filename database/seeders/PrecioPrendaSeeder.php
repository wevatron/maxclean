<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prenda;
use App\Models\PrecioPrenda;

class PrecioPrendaSeeder extends Seeder
{
    public function run(): void
    {
        $precios = [

            // TOALLAS
            'Toalla facial' => ['normal' => 6,  'express' => 10],
            'Toalla de mano' => ['normal' => 15, 'express' => 25],
            'Toalla de baño' => ['normal' => 25, 'express' => 35],

            // SÁBANAS
            'Juego de sábanas' => ['normal' => 10],
            'Sábana bebé'      => ['normal' => 15],

            // COBIJA
            'Cobija' => ['normal' => 40],

            // EDREDONES
            'Edredón individual delgado' => ['normal' => 65, 'paquete' => 150, 'pzs' => 3],
            'Edredón individual normal'  => ['normal' => 90, 'paquete' => 200, 'pzs' => 3],
            'Edredón individual jumbo'   => ['normal' => 100,'paquete' => 230, 'pzs' => 3],

            'Edredón matrimonial normal' => ['normal' => 110],
            'Edredón matrimonial jumbo'  => ['normal' => 130],

            'Edredón Queen normal' => ['normal' => 130],
            'Edredón Queen jumbo'  => ['normal' => 140],

            'Edredón King normal' => ['normal' => 150],
            'Edredón King jumbo'  => ['normal' => 170],

            // ALMOHADAS
            'Almohada normal' => ['normal' => 30],
            'Almohada Sognare' => ['normal' => 50],

            // CUBRECAMAS
            'Cubrecolchón normal'  => ['normal' => 40],
            'Cubrecolchón Sognare' => ['normal' => 70],

            // CHAMARRAS
            'Chamarra' => ['normal' => 50, 'express' => 80],
            'Chaleco'  => ['normal' => 40, 'express' => 70],

            // ROPA INTERIOR
            'Ropa interior por kg' => ['normal' => 40],

            // VARIOS
            'Gorra' => ['normal' => 15],
            'Mochila' => ['normal' => 35],
            'Manteles 1-4' => ['normal' => 10],
            'Manteles 5-9' => ['normal' => 15],
            'Zapatillas / Tenis' => ['normal' => 40],
        ];

        foreach ($precios as $nombre => $data) {
            $prenda = Prenda::where('nombre', $nombre)->first();

            if (!$prenda) continue;

            PrecioPrenda::firstOrCreate([
                'prenda_id' => $prenda->id,
            ], [
                'precio_normal' => $data['normal'] ?? null,
                'precio_express' => $data['express'] ?? null,
                'precio_paquete' => $data['paquete'] ?? null,
                'piezas_por_paquete' => $data['pzs'] ?? null,
            ]);
        }
    }
}
