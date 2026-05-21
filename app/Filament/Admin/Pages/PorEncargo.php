<?php

namespace App\Filament\Admin\Pages;

use App\Models\Cuenta;
use App\Models\Prenda;
use App\Models\Servicio;
use App\Models\Sucursal;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User as Cliente;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class PorEncargo extends Page
{
    public $modoExpress = false;

    protected string $view = 'filament.admin.pages.por-encargo';

    protected static ?string $navigationLabel = 'Por pieza';
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    public $clientePanelAbierto = true;

    public $sucursalId;
    public $items = [];
    public $total = 0;
    public $search = '';
    public $servicioSeleccionado;
    public $prendas = [];

    public $montoPago = 0;
    public $metodoPago = 'efectivo';

    public $accesoValido = false;
    public $mensajeAcceso = null;

    public $modalCobroAbierto = false;
    public $montoTemporal = 0;

    public $procesando = false;

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

            $this->servicioSeleccionado = Servicio::where('activo', true)->first()?->id;
            $this->cargarPrendas();
        } elseif ($sucursales->count() === 0) {
            $this->mensajeAcceso = 'No tiene sucursal asignada.';
        } else {
            $this->mensajeAcceso = 'Tiene más de una sucursal asignada.';
        }

        $this->clientePanelAbierto = true;
    }

    public function updatedSearch()
    {
        $this->cargarPrendas();
    }

    public function cargarPrendas()
    {
        if (! $this->sucursalId) {
            $this->prendas = collect();

            return;
        }

        $sucursalId = $this->sucursalId;

        $this->prendas = Prenda::porPieza()
            ->with([
                'precios' => function ($query) use ($sucursalId) {
                    $query->where('sucursal_id', $sucursalId);
                },
            ])
            ->where('nombre', 'like', "%{$this->search}%")
            ->limit(9)
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
    }

    public function agregarPrenda($id)
    {
        $prenda = Prenda::with([
            'precios' => function ($query) {
                $query->where('sucursal_id', $this->sucursalId);
            },
        ])->find($id);

        if (! $prenda) {
            return;
        }

        $precioRelacion = $prenda->precios->first();

        $precioNormal = $precioRelacion?->precio_normal;
        $precioExpress = $precioRelacion?->precio_express ?? 0;
        $precioPaquete = $precioRelacion?->precio_paquete ?? 0;
        $piezasPorPaquete = $precioRelacion?->piezas_por_paquete ?? 0;

        if (! $precioNormal) {
            Notification::make()
                ->title('Esta prenda no tiene precio configurado en esta sucursal')
                ->danger()
                ->send();

            return;
        }

        foreach ($this->items as $index => $item) {
            if ($item['prenda_id'] == $id) {
                $this->items[$index]['cantidad']++;
                $this->calcularTotal();

                return;
            }
        }

        $this->items[] = [
            'prenda_id'          => $id,
            'nombre'             => $prenda->nombre,
            'precio'             => $precioNormal,
            'precio_normal'      => $precioNormal,
            'precio_express'     => $precioExpress,
            'precio_paquete'     => $precioPaquete,
            'piezas_por_paquete' => $piezasPorPaquete,
            'cantidad'           => 1,
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
        } else {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }

        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $this->total = collect($this->items)->sum(
            fn ($item) => $this->calcularSubtotalItem($item)
        );
    }

    public function calcularSubtotalItem(array $item): float
    {
        $cantidad = (int) ($item['cantidad'] ?? 0);
        $precioNormal = (float) ($item['precio_normal'] ?? $item['precio'] ?? 0);
        $precioExpress = (float) ($item['precio_express'] ?? 0);
        $precioPaquete = (float) ($item['precio_paquete'] ?? 0);
        $piezasPorPaquete = (int) ($item['piezas_por_paquete'] ?? 0);

        if ($this->modoExpress) {
            if ($precioExpress <= 0) {
                return $cantidad * $precioNormal;
            }

            return $cantidad * $precioExpress;
        }

        if ($piezasPorPaquete > 0 && $precioPaquete > 0) {
            $paquetes = intdiv($cantidad, $piezasPorPaquete);
            $sueltas = $cantidad % $piezasPorPaquete;

            return ($paquetes * $precioPaquete) + ($sueltas * $precioNormal);
        }

        return $cantidad * $precioNormal;
    }

    public function updatedModoExpress()
    {
        $this->calcularTotal();
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

    $this->montoTemporal = 0;
    $this->montoPago = 0;
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
        $this->montoTemporal = round($this->total / 2, 2);
    }

    public function montoTotal()
    {
        $this->montoTemporal = $this->total;
    }

public function confirmarCobro()
{
    $this->montoTemporal = (float) ($this->montoTemporal ?: 0);

    if ($this->montoTemporal < 0) {
        Notification::make()
            ->title('Monto inválido')
            ->danger()
            ->send();

        return;
    }

    if ($this->montoTemporal > $this->total) {
        Notification::make()
            ->title('El anticipo no puede ser mayor al total')
            ->danger()
            ->send();

        return;
    }

    $this->montoPago = $this->montoTemporal;
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
                ->title('No hay prendas en el ticket')
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

        $this->procesando = true;

        try {
            DB::transaction(function () {
                $cuenta = $this->obtenerOCrearCuenta();

                $numero = Ticket::generarNumero($this->sucursalId);

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
                    'user_id'     => auth()->id(),
                    'cliente_id'  => $this->clienteSeleccionadoId,
                    'status_id'   => $statusRecibidoId,
                    'numero'      => $numero,
                    'tipo'        => $this->modoExpress ? 'encargo_express' : 'encargo',
                    'total'       => $this->total,
                ]);

                $ticket->forceFill([
                    'cuenta_id' => $cuenta->id,
                ])->save();

                $ticket->procesos()->create([
                    'proceso' => 'detallado',
                    'completado' => false,
                ]);

                foreach ($this->items as $item) {
                    $subtotalItem = $this->calcularSubtotalItem($item);

                    $precioUnitario = $this->modoExpress
                        ? ($item['precio_express'] ?? $item['precio_normal'] ?? $item['precio'])
                        : ($item['precio_normal'] ?? $item['precio']);

                    $ticket->items()->create([
                        'prenda_id'       => $item['prenda_id'],
                        'cantidad'        => $item['cantidad'],
                        'precio_unitario' => $precioUnitario,
                        'subtotal'        => $subtotalItem,
                    ]);
                }

                if ($this->servicioSeleccionado) {
                    $ticket->servicios()->attach($this->servicioSeleccionado);
                }

                if ($this->montoPago > 0) {
                    $pago = $ticket->pagos()->create([
                        'metodo_pago' => $this->metodoPago,
                        'monto'       => $this->montoPago,
                        'user_id'     => auth()->id(),
                        'sucursal_id' => $this->sucursalId ?? auth()->user()->sucursal_id,
                        'cancelado'   => false,
                        'tipo_movimiento' => 'venta',
                    ]);

                    $pago->forceFill([
                        'cuenta_id' => $cuenta->id,
                    ])->save();

                    \App\Models\Punto::create([
                        'user_id'      => $this->clienteSeleccionadoId,
                        'asignado_por' => auth()->id(),
                        'puntos'       => (int) round($this->montoPago),
                        'fecha'        => now(),
                        'tikete'       => $ticket->numero,
                        'sucursal_id'  => $this->sucursalId,
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
                    ->body("Asignado a la cuenta {$cuenta->numero}")
                    ->success()
                    ->send();
            });

            $this->reset([
                'items',
                'total',
                'search',
                'montoPago',
                'montoTemporal',
                'clienteSearch',
                'clientesEncontrados',
                'clienteSeleccionadoId',
                'clienteSeleccionadoNombre',
                'clientePanelAbierto',
                'modoExpress',
                'crearCuentaNueva',
            ]);

            $this->modoExpress = false;
            $this->crearCuentaNueva = false;
            $this->clientePanelAbierto = true;
            $this->metodoPago = 'efectivo';
            $this->modalCobroAbierto = false;
            $this->servicioSeleccionado = Servicio::where('activo', true)->first()?->id;
            $this->cargarPrendas();
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

public function getHeading(): string
{
    return '';
}

public function getTitle(): string
{
    if (! $this->sucursalId) {
        return 'Sin sucursal';
    }

    $sucursal = Sucursal::find($this->sucursalId);

    return 'POR KILO - Sucursal: ' . ($sucursal?->nombre ?? 'Sin nombre');
}

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:PorEncargo');
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
}