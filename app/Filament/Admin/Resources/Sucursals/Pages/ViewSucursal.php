<?php

namespace App\Filament\Admin\Resources\Sucursals\Pages;

use App\Filament\Admin\Resources\Sucursals\SucursalResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSucursal extends ViewRecord
{
    protected static string $resource = SucursalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
