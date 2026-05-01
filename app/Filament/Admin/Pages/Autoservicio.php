<?php

namespace App\Filament\Admin\Pages;

use App\Models\Punto;
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

class Autoservicio extends Page
{
    protected string $view = 'filament.admin.pages.autoservicio';

    protected static ?string $navigationLabel = 'Autoservicio';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public $clientePanelAbierto = true;

    public $sucursalId;
    public $items = [];
    public $total = 0;
    public $search = '';
    public $servicios = [];

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

            $this->cargarServicios();
        } elseif ($sucursales->count() === 0) {
            $this->mensajeAcceso = 'No tiene sucursal asignada.';
        } else {
            $this->mensajeAcceso = 'Tiene más de una sucursal asignada.';
        }

        $this->clientePanelAbierto = true;
    }

    public function updatedSearch()
    {
        $this->cargarServicios();
    }

    public function cargarServicios()
    {
        $this->servicios = Servicio::query()
            ->where('activo', true)
            ->where(function ($query) {
                $query->where('nombre', 'like', "%{$this->search}%")
                    ->orWhere('descripcion', 'like', "%{$this->search}%");
            })
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
    }

    public function agregarServicio($id)
    {
        $servicio = Servicio::where('activo', true)->find($id);

        if (! $servicio) {
            return;
        }

        foreach ($this->items as $index => $item) {
            if ($item['servicio_id'] == $id) {
                $this->items[$index]['cantidad']++;
                $this->items[$index]['subtotal'] = $this->items[$index]['cantidad'] * $this->items[$index]['precio_unitario'];
                $this->calcularTotal();
                return;
            }
        }

        $this->items[] = [
            'servicio_id' => $servicio->id,
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
        $this->total = collect($this->items)->sum('subtotal');
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

        $this->procesando = true;

        try {
            DB::transaction(function () {
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
                    'user_id' => auth()->id(),
                    'cliente_id' => $this->clienteSeleccionadoId,
                    'status_id' => $statusRecibidoId,
                    'numero' => $numero,
                    'tipo' => 'autoservicio',
                    'total' => $this->total,
                ]);

                foreach ($this->items as $item) {
                    $ticket->servicios()->attach($item['servicio_id'], [
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                if ($this->montoPago > 0) {
                    $ticket->pagos()->create([
                        'metodo_pago' => $this->metodoPago,
                        'monto' => $this->montoPago,
                        'user_id' => auth()->id(),
                        'sucursal_id' => $this->sucursalId ?? auth()->user()->sucursal_id,
                        'cancelado' => false,
                        'tipo_movimiento' => 'venta',
                    ]);

                    Punto::create([
                        'user_id' => $this->clienteSeleccionadoId,
                        'asignado_por' => auth()->id(),
                        'puntos' => (int) round($this->montoPago),
                        'fecha' => now(),
                        'tikete' => $ticket->numero,
                        'sucursal_id' => $this->sucursalId,
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
            ]);

            $this->clientePanelAbierto = true;
            $this->metodoPago = 'efectivo';
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

    public function getHeading(): string
    {
        if (! $this->sucursalId) {
            return 'Sin sucursal';
        }

        $sucursal = Sucursal::find($this->sucursalId);

        return 'AUTOSERVICIO - Sucursal: ' . ($sucursal?->nombre ?? 'Sin nombre');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:Autoservicio')
            || auth()->user()?->can('View:PorEncargo');
    }

    public function toggleClientePanel()
    {
        $this->clientePanelAbierto = ! $this->clientePanelAbierto;
    }

    public function getCrearClienteUrl(): string
    {
        return \App\Filament\Admin\Resources\Clientes\ClienteResource::getUrl('create');
    }
}