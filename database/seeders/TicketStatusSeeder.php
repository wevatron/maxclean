<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketStatus;

class TicketStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['nombre' => 'recibido'],
            ['nombre' => 'pagado'],
            ['nombre' => 'proceso'],
            ['nombre' => 'terminado'],
            ['nombre' => 'entregado'],
            ['nombre' => 'cancelado'],
        ];

        foreach ($statuses as $status) {
            TicketStatus::updateOrCreate(
                ['nombre' => $status['nombre']],
                $status
            );
        }
    }
}