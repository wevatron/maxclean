<?php

namespace App\Filament\Admin\Resources\Tickets\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Models\Prenda;
use App\Models\Servicio;
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

    public bool $modalAgregarServicioAbierto = false;
    public string $buscarServicio = '';
    public ?int $servicioSeleccionadoId = null;
    public ?string $servicioSeleccionadoTexto = null;
    public int $cantidadServicio = 1;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('imprimir')
                ->label('Imprimir ticket')
                ->icon('heroicon-m-printer')
                ->color('gray')
                ->url(fn($record) => route('tickets.print', [
                    'ticket' => $record->id,
                    'autoprint' => 1,
                ]))
                ->openUrlInNewTab(),
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

                    $this->refrescarRecord();

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

    public function getServiciosDisponiblesProperty()
    {
        if (($this->record->tipo ?? null) !== 'autoservicio') {
            return collect();
        }

        $texto = trim($this->buscarServicio);

        if ($texto === '') {
            return collect();
        }

        return Servicio::query()
            ->where(function ($query) use ($texto) {
                $query->where('nombre', 'like', "%{$texto}%")
                    ->orWhere('descripcion', 'like', "%{$texto}%");
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

    public function updatedBuscarServicio($value): void
    {
        $value = trim((string) $value);

        if (
            $this->servicioSeleccionadoId &&
            $value !== trim((string) $this->servicioSeleccionadaTexto)
        ) {
            $this->servicioSeleccionadoId = null;
            $this->servicioSeleccionadoTexto = null;
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

    public function abrirModalAgregarServicio(): void
    {
        if (($this->record->tipo ?? null) !== 'autoservicio') {
            Notification::make()
                ->title('Solo disponible en tickets de autoservicio')
                ->warning()
                ->send();
            return;
        }

        $this->servicioSeleccionadoId = null;
        $this->servicioSeleccionadoTexto = null;
        $this->cantidadServicio = 1;
        $this->buscarServicio = '';
        $this->modalAgregarServicioAbierto = true;
    }

    public function cerrarModalAgregarServicio(): void
    {
        $this->modalAgregarServicioAbierto = false;
        $this->servicioSeleccionadoId = null;
        $this->servicioSeleccionadoTexto = null;
        $this->cantidadServicio = 1;
        $this->buscarServicio = '';
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

    public function seleccionarServicioTicket(int $servicioId): void
    {
        $servicio = Servicio::query()->find($servicioId);

        if (! $servicio) {
            Notification::make()
                ->title('El servicio no es válido')
                ->danger()
                ->send();
            return;
        }

        $texto = $servicio->nombre;

        if (! empty($servicio->descripcion)) {
            $texto .= ' - ' . $servicio->descripcion;
        }

        $this->servicioSeleccionadoId = $servicio->id;
        $this->servicioSeleccionadoTexto = $texto;
        $this->buscarServicio = $texto;
    }

    public function limpiarSeleccionPrenda(): void
    {
        $this->prendaSeleccionadaId = null;
        $this->prendaSeleccionadaTexto = null;
        $this->buscarPrenda = '';
    }

    public function limpiarSeleccionServicio(): void
    {
        $this->servicioSeleccionadoId = null;
        $this->servicioSeleccionadoTexto = null;
        $this->buscarServicio = '';
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

    public function agregarServicioTicket(): void
    {
        if (($this->record->tipo ?? null) !== 'autoservicio') {
            Notification::make()
                ->title('Solo disponible en tickets de autoservicio')
                ->danger()
                ->send();
            return;
        }

        if (! $this->servicioSeleccionadoId) {
            Notification::make()
                ->title('Debes seleccionar un servicio')
                ->danger()
                ->send();
            return;
        }

        if ((int) $this->cantidadServicio < 1) {
            Notification::make()
                ->title('La cantidad debe ser al menos 1')
                ->danger()
                ->send();
            return;
        }

        $servicio = Servicio::query()->find($this->servicioSeleccionadoId);

        if (! $servicio) {
            Notification::make()
                ->title('El servicio no es válido')
                ->danger()
                ->send();
            return;
        }

        DB::transaction(function () use ($servicio) {
            $cantidadNueva = (int) $this->cantidadServicio;
            $precioUnitario = (float) ($servicio->precio_base ?? 0);

            $servicioExistente = $this->record->servicios()
                ->where('servicios.id', $servicio->id)
                ->first();

            if ($servicioExistente) {
                $cantidadNueva += (int) ($servicioExistente->pivot->cantidad ?? 0);

                $this->record->servicios()->updateExistingPivot($servicio->id, [
                    'cantidad' => $cantidadNueva,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $cantidadNueva * $precioUnitario,
                ]);
            } else {
                $this->record->servicios()->attach($servicio->id, [
                    'cantidad' => $cantidadNueva,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $cantidadNueva * $precioUnitario,
                ]);
            }

            $this->recalcularTotalAutoservicio();
            $this->actualizarStatusAutoservicioSegunSaldo();
        });

        $this->refrescarRecord();

        Notification::make()
            ->title('Servicio agregado al ticket')
            ->success()
            ->send();

        $this->cerrarModalAgregarServicio();
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

    public function incrementarServicioTicket(int $servicioId): void
    {
        if (($this->record->tipo ?? null) !== 'autoservicio') {
            return;
        }

        $servicio = $this->record->servicios()
            ->where('servicios.id', $servicioId)
            ->first();

        if (! $servicio) {
            return;
        }

        DB::transaction(function () use ($servicioId, $servicio) {
            $cantidad = (int) ($servicio->pivot->cantidad ?? 0) + 1;
            $precioUnitario = (float) ($servicio->pivot->precio_unitario ?? $servicio->precio_base ?? 0);

            $this->record->servicios()->updateExistingPivot($servicioId, [
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $cantidad * $precioUnitario,
            ]);

            $this->recalcularTotalAutoservicio();
            $this->actualizarStatusAutoservicioSegunSaldo();
        });

        $this->refrescarRecord();
    }

    public function disminuirServicioTicket(int $servicioId): void
    {
        if (($this->record->tipo ?? null) !== 'autoservicio') {
            return;
        }

        $servicio = $this->record->servicios()
            ->where('servicios.id', $servicioId)
            ->first();

        if (! $servicio) {
            return;
        }

        DB::transaction(function () use ($servicioId, $servicio) {
            $cantidadActual = (int) ($servicio->pivot->cantidad ?? 0);
            $precioUnitario = (float) ($servicio->pivot->precio_unitario ?? $servicio->precio_base ?? 0);

            if ($cantidadActual > 1) {
                $cantidadNueva = $cantidadActual - 1;

                $this->record->servicios()->updateExistingPivot($servicioId, [
                    'cantidad' => $cantidadNueva,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $cantidadNueva * $precioUnitario,
                ]);
            } else {
                $this->record->servicios()->detach($servicioId);
            }

            $this->recalcularTotalAutoservicio();
            $this->actualizarStatusAutoservicioSegunSaldo();
        });

        $this->refrescarRecord();
    }

    public function quitarServicioTicket(int $servicioId): void
    {
        if (($this->record->tipo ?? null) !== 'autoservicio') {
            return;
        }

        $servicio = $this->record->servicios()
            ->where('servicios.id', $servicioId)
            ->first();

        if (! $servicio) {
            return;
        }

        DB::transaction(function () use ($servicioId) {
            $this->record->servicios()->detach($servicioId);

            $this->recalcularTotalAutoservicio();
            $this->actualizarStatusAutoservicioSegunSaldo();
        });

        $this->refrescarRecord();

        Notification::make()
            ->title('Servicio eliminado del ticket')
            ->success()
            ->send();
    }

    protected function recalcularTotalAutoservicio(): void
    {
        $totalServicios = (float) $this->record->servicios()
            ->sum('ticket_servicios.subtotal');

        $this->record->update([
            'total' => $totalServicios,
        ]);

        $this->record->refresh();
    }

    protected function actualizarStatusAutoservicioSegunSaldo(): void
    {
        if (($this->record->tipo ?? null) !== 'autoservicio') {
            return;
        }

        $this->record->refresh();

        if ($this->record->saldo <= 0) {
            $statusPagadoId = TicketStatus::whereRaw(
                'LOWER(nombre) = ?',
                ['pagado']
            )->value('id');

            if ($statusPagadoId) {
                $this->record->update([
                    'status_id' => $statusPagadoId,
                ]);
            }

            return;
        }

        $statusRecibidoId = TicketStatus::whereRaw(
            'LOWER(nombre) = ?',
            ['recibido']
        )->value('id');

        if ($statusRecibidoId) {
            $this->record->update([
                'status_id' => $statusRecibidoId,
            ]);
        }
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

        $this->refrescarRecord();

        Notification::make()
            ->title('Pago cancelado correctamente')
            ->success()
            ->send();
    }
}
