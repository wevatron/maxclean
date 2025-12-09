<?php

namespace App\Filament\Admin\Resources\CategoriaPrendas\Pages;

use App\Filament\Admin\Resources\CategoriaPrendas\CategoriaPrendaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaPrendas extends ListRecords
{
    protected static string $resource = CategoriaPrendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
