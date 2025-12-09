<?php

namespace App\Filament\Admin\Resources\Prendas\Pages;

use App\Filament\Admin\Resources\Prendas\PrendaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPrenda extends EditRecord
{
    protected static string $resource = PrendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
