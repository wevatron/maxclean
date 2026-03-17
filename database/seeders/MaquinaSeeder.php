<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maquina;
use App\Models\Sucursal;
use App\Models\TipoMaquina;

class MaquinaSeeder extends Seeder
{
    public function run(): void
    {
        $sucursales = Sucursal::whereIn('id', [1, 2])->get();

        if ($sucursales->count() < 2) {
            $this->command->error('Debe crear al menos las sucursales con IDs 1 y 2 antes de ejecutar este seeder.');
            return;
        }

        // Cantidades recomendadas por tipo de máquina
        $cantidadPorTipo = [
            'Lavadora chica'   => 2,
            'Lavadora mediana' => 2,
            'Lavadora grande'  => 1,
            'Secadora chica'   => 2,
            'Secadora mediana' => 2,
            'Secadora grande'  => 1,
        ];

        foreach ($sucursales as $sucursal) {

            foreach ($cantidadPorTipo as $nombreTipo => $cantidad) {

                $tipo = TipoMaquina::where('nombre', $nombreTipo)->first();

                if (!$tipo) {
                    $this->command->warn("Tipo de máquina no encontrado: $nombreTipo");
                    continue;
                }

                for ($i = 1; $i <= $cantidad; $i++) {
                    Maquina::firstOrCreate([
                        'sucursal_id'      => $sucursal->id,
                        'tipo_maquina_id'  => $tipo->id,
                        'status'           => 'libre',
                    ]);
                }
            }
        }

        $this->command->info('Máquinas creadas correctamente para las sucursales 1 y 2.');
    }
}
