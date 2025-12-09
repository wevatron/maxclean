<?php

namespace App\Filament\Admin\Resources\PrecioPrendas\Pages;

use App\Filament\Admin\Resources\PrecioPrendas\PrecioPrendaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrecioPrenda extends ViewRecord
{
    protected static string $resource = PrecioPrendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
