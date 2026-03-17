<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Models\Prenda;
use App\Models\Ticket;
use App\Models\Servicio;
use App\Models\TicketStatus;
use App\Models\Sucursal;
use App\Models\User as Cliente;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class PorEncargo extends Page
{
    public $modoExpress = false;
    protected string $view = 'filament.admin.pages.por-encargo';
    protected static ?string $navigationLabel = 'Por encargo';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
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

        $this->prendas = Prenda::with([
            'precios' => function ($query) use ($sucursalId) {
                $query->where('sucursal_id', $sucursalId);
            }
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
    }
    public function agregarPrenda($id)
    {
        $prenda = Prenda::with([
            'precios' => function ($query) {
                $query->where('sucursal_id', $this->sucursalId);
            }
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
            fn($item) => $this->calcularSubtotalItem($item)
        );
    }

    protected function calcularSubtotalItem(array $item): float
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

        $this->montoTemporal = $this->total;
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
                $numero = Ticket::generarNumero($this->sucursalId);

                $statusRecibidoId = TicketStatus::whereRaw('LOWER(nombre) = ?', ['recibido'])->value('id');
                $statusPagadoId   = TicketStatus::whereRaw('LOWER(nombre) = ?', ['pagado'])->value('id');

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
                    $ticket->pagos()->create([
                        'metodo_pago' => $this->metodoPago,
                        'monto'       => $this->montoPago,
                    ]);

                    $ticket->refresh();

                    if (($ticket->saldo ?? ($ticket->total - $ticket->pagos()->sum('monto'))) <= 0) {
                        $ticket->update([
                            'status_id' => $statusPagadoId,
                        ]);
                    }
                }

                Notification::make()
                    ->title("Ticket #{$ticket->numero} creado")
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
            ]);
            $this->modoExpress = false;
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

    public function getHeading(): string
    {
        if (! $this->sucursalId) {
            return 'Sin sucursal';
        }

        $sucursal = Sucursal::find($this->sucursalId);

        return 'Sucursal: ' . ($sucursal?->nombre ?? 'Sin nombre');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('Punto:Gestionar');
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
