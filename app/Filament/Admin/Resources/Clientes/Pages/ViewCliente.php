<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use App\Filament\Admin\Resources\Clientes\ClienteResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCliente extends ViewRecord
{
    protected static string $resource = ClienteResource::class;

    protected static bool $hasRelationManagers = true;

    public function hasCombinedRelationManagerTabs(): bool
    {
        return true;
    }

    // 👇 ESTA ES LA CLAVE QUE FALTABA 👇
    protected function canCreateRelation(): bool
    {
        return true;
    }

    protected function canEditRelation(): bool
    {
        return true;
    }

    protected function canDeleteRelation(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
