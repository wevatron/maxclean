<?php

namespace App\Filament\Admin\Resources\Descuentos\Pages;

use App\Filament\Admin\Resources\Descuentos\DescuentoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDescuentos extends ListRecords
{
    protected static string $resource = DescuentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
