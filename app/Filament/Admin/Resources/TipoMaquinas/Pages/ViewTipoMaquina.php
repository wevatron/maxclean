<?php

namespace App\Filament\Admin\Resources\TipoMaquinas\Pages;

use App\Filament\Admin\Resources\TipoMaquinas\TipoMaquinaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTipoMaquina extends ViewRecord
{
    protected static string $resource = TipoMaquinaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
