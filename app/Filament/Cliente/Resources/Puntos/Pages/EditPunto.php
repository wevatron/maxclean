<?php

namespace App\Filament\Cliente\Resources\Puntos\Pages;

use App\Filament\Cliente\Resources\Puntos\PuntoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPunto extends EditRecord
{
    protected static string $resource = PuntoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            /* DeleteAction::make(), */
        ];
    }
}
