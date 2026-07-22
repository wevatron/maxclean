<?php

namespace App\Filament\Admin\Resources\HistorialTickets\Pages;

use App\Filament\Admin\Resources\HistorialTickets\HistorialTicketsResource;
use Filament\Resources\Pages\ListRecords;

class ListHistorialTickets extends ListRecords
{
    protected static string $resource = HistorialTicketsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
