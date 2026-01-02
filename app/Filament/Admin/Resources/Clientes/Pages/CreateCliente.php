<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use App\Filament\Admin\Resources\Clientes\ClienteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCliente extends CreateRecord
{
    protected static string $resource = ClienteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return \App\Filament\Admin\Resources\Clientes\Schemas\ClienteForm::mutateDataBeforeCreate($data);
    }

    protected function afterCreate(): void
    {
        // Asignar rol CLIENTE
        $this->record->assignRole('cliente');
    }

}
