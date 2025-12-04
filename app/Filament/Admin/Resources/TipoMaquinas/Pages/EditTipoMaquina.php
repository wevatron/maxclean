<?php

namespace App\Filament\Admin\Resources\TipoMaquinas\Pages;

use App\Filament\Admin\Resources\TipoMaquinas\TipoMaquinaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoMaquina extends EditRecord
{
    protected static string $resource = TipoMaquinaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
