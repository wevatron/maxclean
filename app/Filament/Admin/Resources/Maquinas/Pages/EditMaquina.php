<?php

namespace App\Filament\Admin\Resources\Maquinas\Pages;

use App\Filament\Admin\Resources\Maquinas\MaquinaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMaquina extends EditRecord
{
    protected static string $resource = MaquinaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
