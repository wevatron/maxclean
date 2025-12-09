<?php

namespace App\Filament\Admin\Resources\CategoriaPrendas\Pages;

use App\Filament\Admin\Resources\CategoriaPrendas\CategoriaPrendaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCategoriaPrenda extends ViewRecord
{
    protected static string $resource = CategoriaPrendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
