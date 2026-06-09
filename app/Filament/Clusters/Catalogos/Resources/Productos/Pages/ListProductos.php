<?php

namespace App\Filament\Clusters\Catalogos\Resources\Productos\Pages;

use App\Filament\Clusters\Catalogos\Resources\Productos\ProductoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
