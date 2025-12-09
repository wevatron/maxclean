<?php

namespace App\Filament\Admin\Resources\CategoriaPrendas\Pages;

use App\Filament\Admin\Resources\CategoriaPrendas\CategoriaPrendaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaPrenda extends EditRecord
{
    protected static string $resource = CategoriaPrendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
