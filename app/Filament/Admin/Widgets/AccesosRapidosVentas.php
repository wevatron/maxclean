<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Pages\Autoservicio;
use App\Filament\Admin\Pages\PorEncargo;
use App\Filament\Admin\Pages\PorKilo;
use Filament\Widgets\Widget;

class AccesosRapidosVentas extends Widget
{
    protected string $view = 'filament.admin.widgets.accesos-rapidos-ventas';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -10;

    public function getAccesos(): array
    {
        return [
            [
                'titulo' => 'Por encargo',
                'descripcion' => 'Registrar prendas individuales.',
                'icono' => '🧺',
                'url' => \App\Filament\Admin\Pages\PorEncargo::getUrl(),
                'color' => '#2563eb',
            ],
            [
                'titulo' => 'Por kilo',
                'descripcion' => 'Registrar lavado por peso.',
                'icono' => '⚖️',
                'url' => \App\Filament\Admin\Pages\PorKilo::getUrl(),
                'color' => '#16a34a',
            ],
            [
                'titulo' => 'Autoservicio',
                'descripcion' => 'Renta de máquinas.',
                'icono' => '🫧',
                'url' => \App\Filament\Admin\Pages\Autoservicio::getUrl(),
                'color' => '#7c3aed',
            ],

            // NUEVOS 🔥
            [
                'titulo' => 'Tickets',
                'descripcion' => 'Ver todos los tickets.',
                'icono' => '🎟️',
                'url' => \App\Filament\Admin\Resources\Tickets\TicketResource::getUrl(),
                'color' => '#0ea5e9',
            ],
            [
                'titulo' => 'Clientes',
                'descripcion' => 'Administrar clientes.',
                'icono' => '👥',
                'url' => \App\Filament\Admin\Resources\Clientes\ClienteResource::getUrl(),
                'color' => '#f59e0b',
            ],
            [
                'titulo' => 'Corte de caja',
                'descripcion' => 'Ver ingresos del día.',
                'icono' => '💰',
                'url' => \App\Filament\Admin\Pages\CorteCaja::getUrl(),
                'color' => '#ef4444',
            ],
        ];
    }
}
