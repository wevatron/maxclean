<?php

namespace App\Filament\Admin\Resources\TipoKilos\Pages;

use App\Filament\Admin\Resources\TipoKilos\TipoKiloResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoKilos extends ListRecords
{
    protected static string $resource = TipoKiloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
