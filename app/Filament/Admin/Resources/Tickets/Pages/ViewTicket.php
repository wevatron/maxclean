<?php

namespace App\Filament\Admin\Resources\TicketResource\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected string $view = 'filament.admin.resources.tickets.view-ticket';

    protected function getHeaderActions(): array
    {
        return [

            Action::make('registrarPago')
                ->label('Registrar pago')
                ->icon('heroicon-m-banknotes')
                ->color('success')
                ->visible(fn ($record) => $record->saldo > 0)
                ->action(function ($record) {

                    // Aquí luego abrimos modal si quieres

                }),

        ];
    }
}