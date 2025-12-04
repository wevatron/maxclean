<?php

namespace App\Filament\Admin\Resources\Maquinas\Pages;

use App\Filament\Admin\Resources\Maquinas\MaquinaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMaquina extends ViewRecord
{
    protected static string $resource = MaquinaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
