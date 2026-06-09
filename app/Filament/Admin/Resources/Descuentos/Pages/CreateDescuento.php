<?php

namespace App\Filament\Admin\Resources\Descuentos\Pages;

use App\Filament\Admin\Resources\Descuentos\DescuentoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDescuento extends CreateRecord
{
    protected static string $resource = DescuentoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
