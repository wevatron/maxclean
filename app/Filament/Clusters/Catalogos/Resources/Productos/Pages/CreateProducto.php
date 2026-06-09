<?php

namespace App\Filament\Clusters\Catalogos\Resources\Productos\Pages;

use App\Filament\Clusters\Catalogos\Resources\Productos\ProductoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;
}
