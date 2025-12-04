<?php

namespace App\Filament\Admin\Resources\TipoMaquinas\Pages;

use App\Filament\Admin\Resources\TipoMaquinas\TipoMaquinaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoMaquinas extends ListRecords
{
    protected static string $resource = TipoMaquinaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
