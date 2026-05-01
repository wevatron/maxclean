<?php

namespace App\Filament\Admin\Resources\Tickets\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Models\Prenda;
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

    public bool $modalAgregarPrendaAbierto = false;
    public string $buscarPrenda = '';
    public ?int $prendaSeleccionadaId = null;
    public ?string $prendaSeleccionadaTexto = null;
    public int $cantidadPrenda = 1;

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
                            'monto' => $data['monto'],
                            'user_id' => auth()->id(),
                            'sucursal_id' => $record->sucursal_id ?? auth()->user()->sucursal_id,
                            'cancelado' => false,
                            'tipo_movimiento' => 'venta',
                        ]);

                        if ($record->cliente_id) {
                            \App\Models\Punto::create([
                                'user_id' => $record->cliente_id,
                                'asignado_por' => auth()->id(),
                                'puntos' => (int) round($data['monto']),
                                'fecha' => now(),
                                'tikete' => $record->numero,
                                'sucursal_id' => $record->sucursal_id,
                            ]);
                        }

                        $record->refresh();

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

    public function getPrendasDisponiblesProperty()
    {
        if (($this->record->tipo ?? null) !== 'encargo_kilo') {
            return collect();
        }

        $texto = trim($this->buscarPrenda);

        if ($texto === '') {
            return collect();
        }

        return Prenda::query()
            ->where('unidad', 'kg')
            ->where(function ($query) use ($texto) {
                $query->where('nombre', 'like', "%{$texto}%")
                    ->orWhere('descripcion', 'like', "%{$texto}%")
                    ->orWhere('tamano', 'like', "%{$texto}%");
            })
            ->orderBy('nombre')
            ->limit(12)
            ->get();
    }

    public function updatedBuscarPrenda($value): void
    {
        $value = trim((string) $value);

        if (
            $this->prendaSeleccionadaId &&
            $value !== trim((string) $this->prendaSeleccionadaTexto)
        ) {
            $this->prendaSeleccionadaId = null;
            $this->prendaSeleccionadaTexto = null;
        }
    }

    public function abrirModalAgregarPrenda(): void
    {
        if (($this->record->tipo ?? null) !== 'encargo_kilo') {
            Notification::make()
                ->title('Solo disponible en tickets por kilo')
                ->warning()
                ->send();
            return;
        }

        $this->prendaSeleccionadaId = null;
        $this->prendaSeleccionadaTexto = null;
        $this->cantidadPrenda = 1;
        $this->buscarPrenda = '';
        $this->modalAgregarPrendaAbierto = true;
    }

    public function cerrarModalAgregarPrenda(): void
    {
        $this->modalAgregarPrendaAbierto = false;
        $this->prendaSeleccionadaId = null;
        $this->prendaSeleccionadaTexto = null;
        $this->cantidadPrenda = 1;
        $this->buscarPrenda = '';
    }
    public function seleccionarPrendaInventario(int $prendaId): void
    {
        $prenda = Prenda::query()
            ->where('id', $prendaId)
            ->where('unidad', 'kg')
            ->first();

        if (! $prenda) {
            Notification::make()
                ->title('La prenda no es válida')
                ->danger()
                ->send();
            return;
        }

        $texto = $prenda->nombre;

        if (! empty($prenda->tamano)) {
            $texto .= ' - ' . ucfirst($prenda->tamano);
        }

        $this->prendaSeleccionadaId = $prenda->id;
        $this->prendaSeleccionadaTexto = $texto;
        $this->buscarPrenda = $texto;
    }

    public function limpiarSeleccionPrenda(): void
    {
        $this->prendaSeleccionadaId = null;
        $this->prendaSeleccionadaTexto = null;
        $this->buscarPrenda = '';
    }

    public function agregarPrendaInventario(): void
    {
        if (($this->record->tipo ?? null) !== 'encargo_kilo') {
            Notification::make()
                ->title('Solo disponible en tickets por kilo')
                ->danger()
                ->send();
            return;
        }

        if (! $this->prendaSeleccionadaId) {
            Notification::make()
                ->title('Debes seleccionar una prenda')
                ->danger()
                ->send();
            return;
        }

        if ((int) $this->cantidadPrenda < 1) {
            Notification::make()
                ->title('La cantidad debe ser al menos 1')
                ->danger()
                ->send();
            return;
        }

        $prenda = Prenda::query()
            ->where('id', $this->prendaSeleccionadaId)
            ->where('unidad', 'kg')
            ->first();

        if (! $prenda) {
            Notification::make()
                ->title('La prenda no es válida')
                ->danger()
                ->send();
            return;
        }

        DB::transaction(function () use ($prenda) {
            $itemExistente = $this->record->items()
                ->where('prenda_id', $prenda->id)
                ->first();

            if ($itemExistente) {
                $itemExistente->update([
                    'cantidad' => (int) $itemExistente->cantidad + (int) $this->cantidadPrenda,
                ]);
            } else {
                $this->record->items()->create([
                    'prenda_id' => $prenda->id,
                    'cantidad' => (int) $this->cantidadPrenda,
                    'precio_unitario' => 0,
                    'subtotal' => 0,
                ]);
            }
        });

        $this->refrescarRecord();

        Notification::make()
            ->title('Prenda agregada al inventario del ticket')
            ->success()
            ->send();

        $this->cerrarModalAgregarPrenda();
    }


    public function incrementarItemInventario(int $itemId): void
    {
        if (($this->record->tipo ?? null) !== 'encargo_kilo') {
            return;
        }

        $item = $this->record->items()->find($itemId);

        if (! $item) {
            return;
        }

        $item->update([
            'cantidad' => (int) $item->cantidad + 1,
        ]);

        $this->refrescarRecord();
    }

    public function disminuirItemInventario(int $itemId): void
    {
        if (($this->record->tipo ?? null) !== 'encargo_kilo') {
            return;
        }

        $item = $this->record->items()->find($itemId);

        if (! $item) {
            return;
        }

        if ((int) $item->cantidad > 1) {
            $item->update([
                'cantidad' => (int) $item->cantidad - 1,
            ]);
        } else {
            $item->delete();
        }

        $this->refrescarRecord();
    }

    public function quitarItemInventario(int $itemId): void
    {
        if (($this->record->tipo ?? null) !== 'encargo_kilo') {
            return;
        }

        $item = $this->record->items()->find($itemId);

        if (! $item) {
            return;
        }

        $item->delete();

        $this->refrescarRecord();

        Notification::make()
            ->title('Prenda eliminada del inventario')
            ->success()
            ->send();
    }

    protected function refrescarRecord(): void
    {
        $this->record->refresh();
        $this->record->load([
            'cliente',
            'operador',
            'sucursal',
            'status',
            'items.prenda.precios',
            'pagos',
            'procesos',
            'servicios',
        ]);
    }

    public function marcarProceso($procesoId)
    {
        $proceso = $this->record->procesos()->find($procesoId);

        if (! $proceso || $proceso->completado) {
            return;
        }

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

        if ($indexActual > 0) {
            $procesoAnterior = $ordenProcesos[$indexActual - 1];

            $anteriorCompletado = $this->record->procesos()
                ->where('proceso', $procesoAnterior)
                ->where('completado', true)
                ->exists();

            if (! $anteriorCompletado) {
                Notification::make()
                    ->title('Proceso fuera de orden')
                    ->body('Debes completar primero: ' . ucfirst($procesoAnterior))
                    ->danger()
                    ->send();

                return;
            }
        }

        if ($proceso->proceso === 'entregado' && $this->record->saldo > 0) {
            Notification::make()
                ->title('No se puede entregar')
                ->body('El ticket aún tiene saldo pendiente.')
                ->danger()
                ->send();

            return;
        }

        $proceso->update([
            'completado' => true,
        ]);

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

        Notification::make()
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

        if ($indexActual > 0) {
            $procesoAnteriorNombre = $ordenProcesos[$indexActual - 1];

            $anteriorCompletado = $this->record->procesos()
                ->where('proceso', $procesoAnteriorNombre)
                ->where('completado', true)
                ->exists();

            if (! $anteriorCompletado) {
                Notification::make()
                    ->title('Proceso fuera de orden')
                    ->body('Debes completar primero: ' . ucfirst($procesoAnteriorNombre))
                    ->danger()
                    ->send();

                return;
            }
        }

        if ($proceso->proceso === 'entregado' && $this->record->saldo > 0) {
            Notification::make()
                ->title('No se puede entregar')
                ->body('El ticket aún tiene saldo pendiente.')
                ->danger()
                ->send();

            return;
        }

        $proceso->update([
            'completado' => true,
        ]);

        if (isset($ordenProcesos[$indexActual + 1])) {
            $siguiente = $ordenProcesos[$indexActual + 1];

            if (! $this->record->procesos()->where('proceso', $siguiente)->exists()) {
                $this->record->procesos()->create([
                    'proceso' => $siguiente,
                    'completado' => false,
                ]);
            }
        }

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

        Notification::make()
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
                    ->close(),

                Action::make('cancelar')
                    ->label('No')
                    ->color('gray')
                    ->button()
                    ->close(),
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

            $pago->update([
                'metodo_pago' => 'cancelado',
                'referencia' => 'Pago cancelado manualmente',
                'cancelado' => true,
            ]);

            if ($record->cliente_id) {
                \App\Models\Punto::create([
                    'user_id' => $record->cliente_id,
                    'asignado_por' => auth()->id(),
                    'puntos' => -1 * abs((int) round($monto)),
                    'fecha' => now(),
                    'tikete' => $record->numero,
                    'sucursal_id' => $record->sucursal_id,
                ]);
            }

            $record->refresh();

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
