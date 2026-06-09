<?php

namespace App\Filament\Admin\Resources\Descuentos\Pages;

use App\Filament\Admin\Resources\Descuentos\DescuentoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDescuento extends ViewRecord
{
    protected static string $resource = DescuentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
