<?php

namespace App\Filament\Admin\Resources\TicketResource\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Models\TicketStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

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
                ->form([
                    Select::make('metodo_pago')
                        ->label('Método de pago')
                        ->options([
                            'efectivo' => 'Efectivo',
                            'transferencia' => 'Transferencia',
                            'tarjeta' => 'Tarjeta',
                        ])
                        ->default('efectivo')
                        ->required(),

                    TextInput::make('monto')
                        ->label('Monto')
                        ->numeric()
                        ->minValue(0.01)
                        ->default(fn ($record) => $record->saldo)
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                    if ($data['monto'] <= 0) {
                        Notification::make()
                            ->title('Monto inválido')
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($data['monto'] > $record->saldo) {
                        Notification::make()
                            ->title('El monto no puede ser mayor al saldo')
                            ->danger()
                            ->send();

                        return;
                    }

                    $record->pagos()->create([
                        'metodo_pago' => $data['metodo_pago'],
                        'monto' => $data['monto'],
                    ]);

                    $record->refresh();

                    if ($record->saldo <= 0) {
                        $statusPagadoId = TicketStatus::whereRaw('LOWER(nombre) = ?', ['pagado'])->value('id');

                        if ($statusPagadoId) {
                            $record->update([
                                'status_id' => $statusPagadoId,
                            ]);
                        }
                    }

                    Notification::make()
                        ->title('Pago registrado correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }
}