<?php

namespace App\Filament\Admin\Resources\PrecioPrendas\Pages;

use App\Filament\Admin\Resources\PrecioPrendas\PrecioPrendaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPrecioPrenda extends EditRecord
{
    protected static string $resource = PrecioPrendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
