<?php

namespace App\Filament\Admin\Resources\Sucursals\Pages;

use App\Filament\Admin\Resources\Sucursals\SucursalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSucursals extends ListRecords
{
    protected static string $resource = SucursalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
