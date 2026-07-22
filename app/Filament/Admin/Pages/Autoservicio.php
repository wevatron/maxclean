<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\Cuentas\CuentaResource;
use App\Models\Cuenta;
use App\Models\Descuento;
use App\Models\Punto;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User as Cliente;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DBSchema;
use Illuminate\Support\Str;

class Autoservicio extends Page
{
    protected string $view = 'filament.admin.pages.autoservicio';

    protected static ?string $navigationLabel = 'F4 Autoservicio';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public $clientePanelAbierto = true;

    public $sucursalId;
    public $items = [];
    public $total = 0;
    public $search = '';
    public $servicios = [];
    public $productos = [];

    public $montoPago = 0;
    public $montoRecibido = 0;
    public $montoCambio = 0;
    public $metodoPago = 'efectivo';

    public $accesoValido = false;
    public $mensajeAcceso = null;

    public $modalCobroAbierto = false;
    public $montoTemporal = 0;

    public $procesando = false;
    public $mobileTab = 'cliente';

    public $clienteSearch = '';
    public $clientesEncontrados = [];
    public $clienteSeleccionadoId = null;
    public $clienteSeleccionadoNombre = null;

    public $crearCuentaNueva = false;

    public function mount()
    {
        $sucursales = auth()->user()->sucursales;

        if ($sucursales->count() === 1) {
            $this->sucursalId = $sucursales->first()->id;
            $this->accesoValido = true;

            $this->cargarServicios();
        } elseif ($sucursales->count() === 0) {
            $this->mensajeAcceso = 'No tiene sucursal asignada.';
        } else {
            $this->mensajeAcceso = 'Tiene más de una sucursal asignada.';
        }

        $this->clientePanelAbierto = true;
        $this->mobileTab = 'cliente';
        $this->calcularTotal();
    }

    public function updatedSearch()
    {
        $this->cargarServicios();
    }

    public function cargarServicios()
    {
        $this->servicios = Servicio::query()
            ->where('activo', true)
            ->where('sucursal_id', $this->sucursalId)
            ->where(function ($query) {
                $query->where('nombre', 'like', "%{$this->search}%")
                    ->orWhere('descripcion', 'like', "%{$this->search}%");
            })
            ->limit(12)
            ->get();

        $this->productos = Producto::query()
            ->where('activo', true)
            ->where('sucursal_id', $this->sucursalId)
            ->where('existencia', '>', 0)
            ->where(function ($query) {
                $query->where('nombre', 'like', "%{$this->search}%")
                    ->orWhere('descripcion', 'like', "%{$this->search}%");
            })
            ->orderByDesc('existencia')
            ->limit(12)
            ->get();
    }

    public function updatedClienteSearch()
    {
        $this->buscarClientes();
    }

    public function buscarClientes()
    {
        if (blank($this->clienteSearch)) {
            $this->clientesEncontrados = [];

            return;
        }

        $texto = trim($this->clienteSearch);

        $this->clientesEncontrados = Cliente::query()
            ->whereHas('roles', function ($query) {
                $query->where('name', 'cliente');
            })
            ->where(function ($query) use ($texto) {
                $query->where('name', 'like', "%{$texto}%")
                    ->orWhere('whatsapp', 'like', "%{$texto}%")
                    ->orWhere('email', 'like', "%{$texto}%");
            })
            ->limit(8)
            ->get()
            ->toArray();
    }

    public function seleccionarCliente($id)
    {
        $cliente = Cliente::find($id);

        if (! $cliente) {
            return;
        }

        $this->clienteSeleccionadoId = $cliente->id;
        $this->clienteSeleccionadoNombre = $cliente->name;
        $this->clienteSearch = $cliente->name;
        $this->clientesEncontrados = [];
        $this->clientePanelAbierto = false;
    }

    public function limpiarCliente()
    {
        $this->clienteSeleccionadoId = null;
        $this->clienteSeleccionadoNombre = null;
        $this->clienteSearch = '';
        $this->clientesEncontrados = [];
        $this->clientePanelAbierto = true;
        $this->crearCuentaNueva = false;
        $this->mobileTab = 'cliente';
    }

    public function agregarServicio($id)
    {
        $this->agregarItem('servicio', (int) $id);
    }

    public function agregarProducto($id)
    {
        $this->agregarItem('producto', (int) $id);
    }

    public function agregarItem(string $tipo, int $id)
    {
        if ($tipo === 'producto') {
            $producto = Producto::where('activo', true)
                ->where('sucursal_id', $this->sucursalId)
                ->where('existencia', '>', 0)
                ->find($id);

            if (! $producto) {
                Notification::make()
                    ->title('Producto no disponible')
                    ->danger()
                    ->send();

                return;
            }

            $cantidadActual = collect($this->items)
                ->where('tipo', 'producto')
                ->where('item_id', $id)
                ->sum('cantidad');

            if ($cantidadActual >= (int) $producto->existencia) {
                Notification::make()
                    ->title('Sin existencia suficiente')
                    ->body("Solo hay {$producto->existencia} unidades disponibles.")
                    ->danger()
                    ->send();

                return;
            }

            foreach ($this->items as $index => $item) {
                if (($item['tipo'] ?? null) === 'producto' && $item['item_id'] == $id) {
                    $this->items[$index]['cantidad']++;
                    $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];
                    $this->calcularTotal();

                    return;
                }
            }

            $this->items[] = [
                'tipo' => 'producto',
                'item_id' => $producto->id,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'cantidad' => 1,
                'precio_unitario' => (float) $producto->precio_base,
                'subtotal' => (float) $producto->precio_base,
            ];

            $this->calcularTotal();

            return;
        }

        $servicio = Servicio::where('activo', true)
            ->where('sucursal_id', $this->sucursalId)
            ->find($id);

        if (! $servicio) {
            return;
        }

        foreach ($this->items as $index => $item) {
            if (($item['tipo'] ?? 'servicio') === 'servicio' && $item['item_id'] == $id) {
                $this->items[$index]['cantidad']++;
                $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];
                $this->calcularTotal();

                return;
            }
        }

        $this->items[] = [
            'tipo' => 'servicio',
            'item_id' => $servicio->id,
            'nombre' => $servicio->nombre,
            'descripcion' => $servicio->descripcion,
            'cantidad' => 1,
            'precio_unitario' => (float) $servicio->precio_base,
            'subtotal' => (float) $servicio->precio_base,
        ];

        $this->calcularTotal();
    }

    public function eliminarItem($index)
    {
        if (! isset($this->items[$index])) {
            return;
        }

        if ($this->items[$index]['cantidad'] > 1) {
            $this->items[$index]['cantidad']--;
            $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];
        } else {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }

        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $this->total = round((float) collect($this->items)->sum('subtotal'), 2);
    }

    public function abrirModalCobro()
    {
        if (empty($this->items)) {
            Notification::make()
                ->title('Ticket vacío')
                ->danger()
                ->send();

            return;
        }

        if (! $this->clienteSeleccionadoId) {
            Notification::make()
                ->title('Debes seleccionar un cliente')
                ->danger()
                ->send();

            return;
        }

        $this->calcularTotal();

        $this->montoTemporal = 0;
        $this->montoPago = 0;
        $this->montoRecibido = 0;
        $this->montoCambio = 0;
        $this->crearCuentaNueva = false;
        $this->modalCobroAbierto = true;
    }

    public function cerrarModalCobro()
    {
        $this->modalCobroAbierto = false;
    }

    public function montoCero()
    {
        $this->montoTemporal = 0;
    }

    public function montoMitad()
    {
        $this->calcularTotal();
        $this->montoTemporal = round($this->totalConDescuento / 2, 2);
    }

    public function montoTotal()
    {
        $this->calcularTotal();
        $this->montoTemporal = $this->totalConDescuento;
    }

    public function updatedMontoTemporal(): void
    {
        $this->montoTemporal = (float) ($this->montoTemporal ?: 0);
        $this->montoCambio = max((float) ($this->montoRecibido ?: 0) - $this->montoTemporal, 0);
    }

    public function updatedMontoRecibido(): void
    {
        $this->montoRecibido = (float) ($this->montoRecibido ?: 0);
        $this->montoCambio = max($this->montoRecibido - (float) ($this->montoTemporal ?: 0), 0);
    }

    public function confirmarCobro()
    {
        $this->calcularTotal();

        $this->montoTemporal = (float) ($this->montoTemporal ?: 0);
        $this->montoRecibido = (float) ($this->montoRecibido ?: 0);
        $totalAPagar = (float) $this->totalConDescuento;

        if ($this->montoTemporal < 0) {
            Notification::make()
                ->title('Monto inválido')
                ->danger()
                ->send();

            return;
        }

        if ($this->montoTemporal > $totalAPagar) {
            Notification::make()
                ->title('El anticipo no puede ser mayor al total')
                ->danger()
                ->send();

            return;
        }

        $this->montoPago = $this->montoTemporal;
        $this->montoCambio = max($this->montoRecibido - $this->montoPago, 0);
        $this->modalCobroAbierto = false;

        $this->crearTicket();
    }

    public function crearTicket()
    {
        if ($this->procesando) {
            return;
        }

        if (empty($this->items)) {
            Notification::make()
                ->title('No hay servicios en el ticket')
                ->danger()
                ->send();

            return;
        }

        if (! $this->clienteSeleccionadoId) {
            Notification::make()
                ->title('Debes seleccionar un cliente')
                ->danger()
                ->send();

            return;
        }

        $this->calcularTotal();

        $this->procesando = true;

        try {
            DB::transaction(function () {
                $cuenta = $this->obtenerOCrearCuenta();

                $numero = Ticket::generarNumero($this->sucursalId);
                $descuentoAplicado = round(max((float) $this->total - (float) $this->totalConDescuento, 0), 2);

                $statusRecibidoId = TicketStatus::whereRaw('LOWER(nombre) = ?', ['recibido'])->value('id');
                $statusPagadoId = TicketStatus::whereRaw('LOWER(nombre) = ?', ['pagado'])->value('id');

                if (! $statusRecibidoId) {
                    throw new \Exception('No existe el status "recibido" en ticket_statuses.');
                }

                if (! $statusPagadoId) {
                    throw new \Exception('No existe el status "pagado" en ticket_statuses.');
                }

                $ticket = Ticket::create([
                    'sucursal_id' => $this->sucursalId,
                    'user_id' => auth()->id(),
                    'cliente_id' => $this->clienteSeleccionadoId,
                    'status_id' => $statusRecibidoId,
                    'numero' => $numero,
                    'tipo' => 'autoservicio',
                    'total' => $this->totalConDescuento,
                ]);

                if (DBSchema::hasColumn('tickets', 'descuento_aplicado')) {
                    $ticket->forceFill([
                        'descuento_aplicado' => $descuentoAplicado,
                    ])->save();
                }

                $ticket->forceFill([
                    'cuenta_id' => $cuenta->id,
                ])->save();

                foreach ($this->items as $item) {
                    if (($item['tipo'] ?? 'servicio') === 'producto') {
                        $producto = Producto::query()
                            ->where('activo', true)
                            ->where('sucursal_id', $this->sucursalId)
                            ->whereKey($item['item_id'])
                            ->lockForUpdate()
                            ->first();

                        if (! $producto) {
                            throw new \Exception("El producto {$item['nombre']} ya no está disponible.");
                        }

                        if ((int) $producto->existencia < (int) $item['cantidad']) {
                            throw new \Exception("No hay existencia suficiente para {$producto->nombre}.");
                        }

                        $producto->decrement('existencia', (int) $item['cantidad']);

                        $ticket->productos()->attach($producto->id, [
                            'cantidad' => $item['cantidad'],
                            'precio_unitario' => $item['precio_unitario'],
                            'subtotal' => $item['subtotal'],
                        ]);

                        continue;
                    }

                    $ticket->servicios()->attach($item['item_id'], [
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                if ($this->montoPago > 0) {
                    $pago = $ticket->pagos()->create([
                        'metodo_pago' => $this->metodoPago,
                        'monto' => $this->montoPago,
                        'user_id' => auth()->id(),
                        'sucursal_id' => $this->sucursalId ?? auth()->user()->sucursal_id,
                        'cancelado' => false,
                        'tipo_movimiento' => 'venta',
                    ]);

                    $pago->forceFill([
                        'cuenta_id' => $cuenta->id,
                    ])->save();

                    Punto::create([
                        'user_id' => $this->clienteSeleccionadoId,
                        'asignado_por' => auth()->id(),
                        'puntos' => (int) round($this->montoPago),
                        'fecha' => now(),
                        'tikete' => $ticket->numero,
                        'sucursal_id' => $this->sucursalId,
                    ]);

                    $ticket->refresh();

                    if (($ticket->saldo ?? ($ticket->total - $ticket->pagos()->where('cancelado', false)->sum('monto'))) <= 0) {
                        $ticket->update([
                            'status_id' => $statusPagadoId,
                        ]);
                    }
                }

                $this->recalcularCuenta($cuenta);

                Notification::make()
                    ->title("Ticket #{$ticket->numero} creado")
                    ->body(
                        "Asignado a la cuenta {$cuenta->numero}. " .
                        "Se guardó el pago con éxito. " .
                        'Es $' . number_format((float) $this->montoCambio, 2) . ' de cambio.'
                    )
                    ->actions([
                        Action::make('verCuenta')
                            ->label('Ver cuenta')
                            ->url(CuentaResource::getUrl('edit', [
                                'record' => $cuenta,
                            ]), true),
                    ])
                    ->success()
                    ->send();
            });

            $this->reset([
                'items',
                'total',
                'search',
                'productos',
                'montoPago',
                'montoRecibido',
                'montoCambio',
                'montoTemporal',
                'clienteSearch',
                'clientesEncontrados',
                'clienteSeleccionadoId',
                'clienteSeleccionadoNombre',
                'clientePanelAbierto',
                'crearCuentaNueva',
                'mobileTab',
            ]);

            $this->crearCuentaNueva = false;
            $this->clientePanelAbierto = true;
            $this->mobileTab = 'cliente';
            $this->metodoPago = 'efectivo';
            $this->montoRecibido = 0;
            $this->montoCambio = 0;
            $this->modalCobroAbierto = false;

            $this->cargarServicios();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error al crear ticket')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->procesando = false;
        }
    }

    protected function obtenerOCrearCuenta(): Cuenta
    {
        if (! $this->crearCuentaNueva) {
            $cuenta = Cuenta::query()
                ->where('cliente_id', $this->clienteSeleccionadoId)
                ->where('sucursal_id', $this->sucursalId)
                ->whereIn('estatus', ['abierta', 'parcial'])
                ->whereDate('abierta_en', now()->toDateString())
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($cuenta) {
                return $cuenta;
            }
        }

        $cuenta = new Cuenta();

        $cuenta->forceFill([
            'cliente_id' => $this->clienteSeleccionadoId,
            'sucursal_id' => $this->sucursalId,
            'user_id' => auth()->id(),
            'numero' => null,
            'total' => 0,
            'total_pagado' => 0,
            'saldo' => 0,
            'estatus' => 'abierta',
            'abierta_en' => now(),
            'cerrada_en' => null,
            'notas' => null,
        ])->save();

        $cuenta->forceFill([
            'numero' => $this->generarNumeroCuenta($cuenta),
        ])->save();

        return $cuenta;
    }

    protected function generarNumeroCuenta(Cuenta $cuenta): string
    {
        return 'C-' . str_pad((string) $cuenta->id, 6, '0', STR_PAD_LEFT);
    }

    protected function recalcularCuenta(Cuenta $cuenta): void
    {
        $ticketIds = Ticket::query()
            ->where('cuenta_id', $cuenta->id)
            ->pluck('id');

        $total = Ticket::query()
            ->where('cuenta_id', $cuenta->id)
            ->sum('total');

        $totalPagado = DB::table('ticket_pagos')
            ->whereIn('ticket_id', $ticketIds)
            ->where('cancelado', false)
            ->sum('monto');

        $saldo = max((float) $total - (float) $totalPagado, 0);

        $estatus = 'abierta';

        if ($saldo <= 0 && $total > 0) {
            $estatus = 'pagada';
        } elseif ($totalPagado > 0 && $saldo > 0) {
            $estatus = 'parcial';
        }

        $cuenta->forceFill([
            'total' => $total,
            'total_pagado' => $totalPagado,
            'saldo' => $saldo,
            'estatus' => $estatus,
            'cerrada_en' => $estatus === 'pagada' ? now() : null,
        ])->save();
    }

    public function getDescuentoGlobalActivoProperty(): ?Descuento
    {
        return Descuento::query()
            ->where('nivel', 'global')
            ->where('activo', true)
            ->whereDate('inicio', '<=', Carbon::today())
            ->whereDate('fin', '>=', Carbon::today())
            ->orderByDesc('id')
            ->first();
    }

    public function getMontoDescuentoProperty(): float
    {
        $descuento = $this->descuentoGlobalActivo;

        if (! $descuento) {
            return 0.0;
        }

        $total = (float) $this->total;

        if (! is_null($descuento->porcentaje)) {
            return round($total * ((float) $descuento->porcentaje / 100), 2);
        }

        if (! is_null($descuento->fijo)) {
            return round((float) $descuento->fijo, 2);
        }

        return 0.0;
    }

    public function getTotalConDescuentoProperty(): float
    {
        return max((float) $this->total - (float) $this->montoDescuento, 0);
    }

    public function getEtiquetaDescuentoProperty(): ?string
    {
        $descuento = $this->descuentoGlobalActivo;

        if (! $descuento) {
            return null;
        }

        if (! is_null($descuento->porcentaje)) {
            return number_format((float) $descuento->porcentaje, 2) . '%';
        }

        if (! is_null($descuento->fijo)) {
            return '$' . number_format((float) $descuento->fijo, 2);
        }

        return null;
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getTitle(): string
    {
        return '';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:Autoservicio');
    }

    public function toggleClientePanel()
    {
        $this->clientePanelAbierto = ! $this->clientePanelAbierto;
    }

    public function abrirClientePanel()
    {
        $this->clientePanelAbierto = true;
    }

    public function cerrarClientePanel()
    {
        $this->clientePanelAbierto = false;
    }

    public function getCrearClienteUrl(): string
    {
        return \App\Filament\Admin\Resources\Clientes\ClienteResource::getUrl('create');
    }

    public function getSucursalNombreCortoProperty(): string
    {
        $sucursal = Sucursal::query()->find($this->sucursalId);
        $nombre = trim((string) ($sucursal?->nombre ?? 'Sucursal'));

        return Str::limit($nombre, 35, '');
    }

    public function getCantidadItemSeleccionado(string $tipo, int $id): int
    {
        return collect($this->items)
            ->where('tipo', $tipo)
            ->where('item_id', $id)
            ->sum('cantidad');
    }

    public function setMobileTab(string $tab): void
    {
        if (! in_array($tab, ['cliente', 'catalogo', 'resumen'], true)) {
            return;
        }

        $this->mobileTab = $tab;
    }
}
