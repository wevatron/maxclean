<?php

namespace App\Filament\Admin\Resources\Tickets\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Models\TicketStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected string $view = 'filament.admin.resources.tickets.view-ticket';

    protected $listeners = [
        'ejecutarCancelacion' => 'cancelarPago',
        'ejecutarProceso' => 'marcarProceso',
    ];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('registrarPago')
                ->label('Registrar pago')
                ->icon('heroicon-m-banknotes')
                ->color('success')
                ->visible(fn($record) => $record->saldo > 0)
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
                        ->readOnly()
                        ->default(fn($record) => $record->saldo)
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

                    DB::transaction(function () use ($data, $record) {

                        $record->pagos()->create([
                            'metodo_pago' => $data['metodo_pago'],
                            'monto'       => $data['monto'],
                            'user_id'     => auth()->id(),
                            'sucursal_id' => $record->sucursal_id ?? auth()->user()->sucursal_id,
                            'cancelado'   => false,
                            'tipo_movimiento' => 'venta',
                        ]);

                        // 🎁 Crear puntos
                        if ($record->cliente_id) {
                            \App\Models\Punto::create([
                                'user_id'      => $record->cliente_id,
                                'asignado_por' => auth()->id(),
                                'puntos'       => (int) round($data['monto']),
                                'fecha'        => now(),
                                'tikete'       => $record->numero,
                                'sucursal_id'  => $record->sucursal_id,
                            ]);
                        }

                        $record->refresh();

                        // Si ya no hay saldo → marcar pagado
                        if ($record->saldo <= 0) {
                            $statusPagadoId = TicketStatus::whereRaw(
                                'LOWER(nombre) = ?',
                                ['pagado']
                            )->value('id');

                            if ($statusPagadoId) {
                                $record->update([
                                    'status_id' => $statusPagadoId,
                                ]);
                            }
                        }
                    });

                    Notification::make()
                        ->title('Pago registrado correctamente')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function marcarProceso($procesoId)
    {
        $proceso = $this->record->procesos()->find($procesoId);

        if (! $proceso || $proceso->completado) {
            return;
        }

        /*
    |--------------------------------------------------------------------------
    | 📌 ORDEN OFICIAL DE PROCESOS
    |--------------------------------------------------------------------------
    */

        $ordenProcesos = [
            'detallado',
            'lavado',
            'secado',
            'doblado y empaquetado',
            'entregado',
        ];

        $indexActual = array_search($proceso->proceso, $ordenProcesos);

        if ($indexActual === false) {
            return;
        }

        /*
    |--------------------------------------------------------------------------
    | 🔒 VALIDAR ORDEN SECUENCIAL (NO SALTAR PROCESOS)
    |--------------------------------------------------------------------------
    */

        if ($indexActual > 0) {

            $procesoAnterior = $ordenProcesos[$indexActual - 1];

            $anteriorCompletado = $this->record->procesos()
                ->where('proceso', $procesoAnterior)
                ->where('completado', true)
                ->exists();

            if (! $anteriorCompletado) {

                \Filament\Notifications\Notification::make()
                    ->title('Proceso fuera de orden')
                    ->body("Debes completar primero: " . ucfirst($procesoAnterior))
                    ->danger()
                    ->send();

                return;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | 🔒 VALIDAR ENTREGA PAGADA
    |--------------------------------------------------------------------------
    */

        if ($proceso->proceso === 'entregado' && $this->record->saldo > 0) {

            \Filament\Notifications\Notification::make()
                ->title('No se puede entregar')
                ->body('El ticket aún tiene saldo pendiente.')
                ->danger()
                ->send();

            return;
        }

        /*
    |--------------------------------------------------------------------------
    | ✅ MARCAR COMO COMPLETADO
    |--------------------------------------------------------------------------
    */

        $proceso->update([
            'completado' => true,
        ]);

        /*
    |--------------------------------------------------------------------------
    | 🔄 ACTUALIZAR STATUS SEGÚN PROCESO
    |--------------------------------------------------------------------------
    */

        switch ($proceso->proceso) {

            case 'detallado':
                $this->record->update(['status_id' => 3]);
                break;

            case 'lavado':
                $this->record->update(['status_id' => 3]);
                break;

            case 'doblado y empaquetado':
                $this->record->update(['status_id' => 4]);
                break;

            case 'entregado':
                $this->record->update(['status_id' => 5]);
                break;
        }

        /*
    |--------------------------------------------------------------------------
    | ➕ CREAR SIGUIENTE PROCESO SECUENCIAL
    |--------------------------------------------------------------------------
    */

        if (isset($ordenProcesos[$indexActual + 1])) {

            $siguiente = $ordenProcesos[$indexActual + 1];

            if (! $this->record->procesos()->where('proceso', $siguiente)->exists()) {

                $this->record->procesos()->create([
                    'proceso' => $siguiente,
                    'completado' => false,
                ]);
            }
        }

        $this->record->refresh();

        \Filament\Notifications\Notification::make()
            ->title('Proceso completado')
            ->success()
            ->send();
    }


    public function confirmarProceso(int $procesoId): void
    {
        $proceso = $this->record->procesos()->findOrFail($procesoId);

        if (! $this->record->puedeCompletar($proceso->proceso)) {
            return;
        }

        if ($proceso->completado) {
            return;
        }

        $ordenProcesos = \App\Models\Ticket::ordenProcesos();

        $indexActual = array_search($proceso->proceso, $ordenProcesos);

        if ($indexActual === false) {
            return;
        }

        /*
    |--------------------------------------------------------------------------
    | 🔒 VALIDAR PROCESO ANTERIOR
    |--------------------------------------------------------------------------
    */

        if ($indexActual > 0) {

            $procesoAnteriorNombre = $ordenProcesos[$indexActual - 1];

            $anteriorCompletado = $this->record->procesos()
                ->where('proceso', $procesoAnteriorNombre)
                ->where('completado', true)
                ->exists();

            if (! $anteriorCompletado) {

                \Filament\Notifications\Notification::make()
                    ->title('Proceso fuera de orden')
                    ->body("Debes completar primero: " . ucfirst($procesoAnteriorNombre))
                    ->danger()
                    ->send();

                return;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | 🔒 VALIDAR ENTREGA PAGADA
    |--------------------------------------------------------------------------
    */

        if ($proceso->proceso === 'entregado' && $this->record->saldo > 0) {

            \Filament\Notifications\Notification::make()
                ->title('No se puede entregar')
                ->body('El ticket aún tiene saldo pendiente.')
                ->danger()
                ->send();

            return;
        }

        /*
    |--------------------------------------------------------------------------
    | ✅ MARCAR COMO COMPLETADO
    |--------------------------------------------------------------------------
    */

        $proceso->update([
            'completado' => true,
        ]);

        /*
    |--------------------------------------------------------------------------
    | ➕ CREAR SIGUIENTE PROCESO
    |--------------------------------------------------------------------------
    */

        if (isset($ordenProcesos[$indexActual + 1])) {

            $siguiente = $ordenProcesos[$indexActual + 1];

            if (! $this->record->procesos()->where('proceso', $siguiente)->exists()) {

                $this->record->procesos()->create([
                    'proceso' => $siguiente,
                    'completado' => false,
                ]);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | 🎯 ACTUALIZAR STATUS
    |--------------------------------------------------------------------------
    */

        switch ($proceso->proceso) {

            case 'detallado':
                $this->record->update(['status_id' => 2]);
                break;

            case 'lavado':
                $this->record->update(['status_id' => 3]);
                break;

            case 'doblado y empaquetado':
                $this->record->update(['status_id' => 4]);
                break;

            case 'entregado':
                $this->record->update(['status_id' => 5]);
                break;
        }

        $this->record->refresh();

        \Filament\Notifications\Notification::make()
            ->title('Proceso completado')
            ->success()
            ->send();
    }
    public function confirmarCancelacion($pagoId)
    {
        Notification::make()
            ->title('¿Cancelar este pago?')
            ->body('Esta acción generará un movimiento negativo y restará los puntos asignados.')
            ->warning()
            ->actions([
                Action::make('confirmar')
                    ->label('Sí, cancelar')
                    ->color('danger')
                    ->button()
                    ->dispatch('ejecutarCancelacion', ['pagoId' => $pagoId])
                    ->close(), // 🔥 esto cierra la notificación

                Action::make('cancelar')
                    ->label('No')
                    ->color('gray')
                    ->button()
                    ->close(), // 🔥 esto también la cierra
            ])
            ->send();
    }


    public function cancelarPago($pagoId)
    {
        $record = $this->record;

        $pago = $record->pagos()->find($pagoId);

        if (! $pago) {
            Notification::make()
                ->title('Pago no encontrado')
                ->danger()
                ->send();
            return;
        }

        if ($pago->metodo_pago === 'cancelado') {
            Notification::make()
                ->title('Este pago ya fue cancelado')
                ->warning()
                ->send();
            return;
        }

        DB::transaction(function () use ($pago, $record) {

            $monto = $pago->monto;

            // 🔁 Marcar como cancelado
            $pago->update([
                'metodo_pago' => 'cancelado',
                'referencia'  => 'Pago cancelado manualmente',
                'cancelado' => true,
            ]);

            // 🎁 Restar puntos
            if ($record->cliente_id) {
                \App\Models\Punto::create([
                    'user_id'      => $record->cliente_id,
                    'asignado_por' => auth()->id(),
                    'puntos'       => -1 * abs((int) round($monto)),
                    'fecha'        => now(),
                    'tikete'       => $record->numero,
                    'sucursal_id'  => $record->sucursal_id,
                ]);
            }

            $record->refresh();

            // 🔄 Si ahora hay saldo → volver a recibido
            if ($record->saldo > 0) {
                $statusRecibidoId = TicketStatus::whereRaw(
                    'LOWER(nombre) = ?',
                    ['recibido']
                )->value('id');

                if ($statusRecibidoId) {
                    $record->update([
                        'status_id' => $statusRecibidoId,
                    ]);
                }
            }
        });

        Notification::make()
            ->title('Pago cancelado correctamente')
            ->success()
            ->send();
    }
    
}
