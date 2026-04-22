<?php

namespace App\Filament\Cliente\Resources\Tickets\Pages;

use App\Filament\Cliente\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected string $view = 'filament.cliente.resources.tickets.pages.view-ticket';

    protected function authorizeAccess(): void
    {
        abort_unless(
            $this->record->cliente_id === auth()->id(),
            403
        );
    }

    protected function getHeaderActions(): array
    {
        return []; // 🔒 Cliente no puede hacer nada
    }
}