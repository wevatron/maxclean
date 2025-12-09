<?php

namespace App\Filament\Admin\Resources\PrecioPrendas\Pages;

use App\Filament\Admin\Resources\PrecioPrendas\PrecioPrendaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrecioPrendas extends ListRecords
{
    protected static string $resource = PrecioPrendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
