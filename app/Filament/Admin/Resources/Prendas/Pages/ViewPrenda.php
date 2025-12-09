<?php

namespace App\Filament\Admin\Resources\Prendas\Pages;

use App\Filament\Admin\Resources\Prendas\PrendaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrenda extends ViewRecord
{
    protected static string $resource = PrendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
