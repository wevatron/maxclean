<?php

namespace App\Filament\Admin\Resources\Cuentas\Pages;

use App\Filament\Admin\Resources\Cuentas\CuentaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCuentas extends ListRecords
{
    protected static string $resource = CuentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            /* CreateAction::make(), */
        ];
    }
}
