<?php

namespace App\Filament\Cliente\Resources\Puntos\Pages;

use App\Filament\Cliente\Resources\Puntos\PuntoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPuntos extends ListRecords
{
    protected static string $resource = PuntoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            /* CreateAction::make(), */
        ];
    }

       public function getSubheading(): ?string
    {
        return 'Historial de puntos que has obtenido en tus visitas';
    }
}
