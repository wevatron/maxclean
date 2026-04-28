<?php

namespace App\Filament\Admin\Pages;

use App\Models\CorteCaja as CorteModel;
use App\Models\TicketPago;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class CorteCaja extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected string $view = 'filament.admin.pages.corte-caja';

    public $fecha;
    public $turno = 'matutino';

    public $resumen = [];
    public $pagos = [];

    public function mount(): void
    {
        $this->fecha = now()->toDateString();
        $this->cargarPagos();
    }

    public function cargarPagos(): void
    {
        $this->pagos = TicketPago::query()
            ->with('ticket')
            ->whereNull('corte_id')
            ->where(function ($q) {
                $q->where('cancelado', false)
                    ->orWhereNull('cancelado');
            })
            ->whereDate('created_at', $this->fecha)
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        $ventas = $this->pagos->filter(
            fn($p) => ($p->tipo_movimiento ?? 'venta') === 'venta'
        );

        $dotaciones = $this->pagos->where('tipo_movimiento', 'dotacion');
        $gastos = $this->pagos->where('tipo_movimiento', 'gasto');

        $totalVentas = $ventas->sum('monto');
        $totalDotaciones = $dotaciones->sum('monto');
        $totalGastos = $gastos->sum('monto');

        $this->resumen = [
            'ventas' => $totalVentas,
            'dotaciones' => $totalDotaciones,
            'gastos' => $totalGastos,
            'saldo' => ($totalVentas + $totalDotaciones) - $totalGastos,

            // Por si luego los necesitas para otros reportes
            'efectivo' => $ventas->where('metodo_pago', 'efectivo')->sum('monto'),
            'tarjeta' => $ventas->where('metodo_pago', 'tarjeta')->sum('monto'),
            'transferencia' => $ventas->where('metodo_pago', 'transferencia')->sum('monto'),
            'total' => $totalVentas,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('descargar_corte_por_fecha')
                ->label('Ver Corte')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->form([
                    DatePicker::make('fecha')
                        ->label('Seleccionar Fecha')
                        ->required()
                        ->live()
                        ->default(now()),

                    Select::make('corte_id')
                        ->label('Corte Disponible')
                        ->options(function (callable $get) {
                            if (! $get('fecha')) {
                                return [];
                            }

                            return CorteModel::query()
                                ->whereDate('fecha', $get('fecha'))
                                ->where('user_id', auth()->id())
                                ->latest('id')
                                ->get()
                                ->mapWithKeys(fn($corte) => [
                                    $corte->id =>
                                    'Corte #' . $corte->id .
                                        ' | Turno: ' . ucfirst($corte->turno) .
                                        ' | Total: $' . number_format($corte->total, 2),
                                ]);
                        })
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $url = route('cortes-caja.pdf', [
                        'corte' => $data['corte_id'],
                    ]);

                    $this->js('window.open(' . json_encode($url) . ", '_blank')");
                }),

            Action::make('dotar')
                ->label('Dotar Caja')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->form([
                    TextInput::make('monto')
                        ->label('Monto')
                        ->numeric()
                        ->required(),

                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    TicketPago::create([
                        'ticket_id' => null,
                        'metodo_pago' => 'efectivo',
                        'monto' => $data['monto'],
                        'referencia' => $data['descripcion'],
                        'cancelado' => false,
                        'user_id' => auth()->id(),
                        'sucursal_id' => 1,
                        'tipo_movimiento' => 'dotacion',
                        'categoria' => 'dotacion',
                        'descripcion' => $data['descripcion'],
                    ]);

                    $this->cargarPagos();

                    Notification::make()
                        ->title('Dotación registrada')
                        ->success()
                        ->send();
                }),

            Action::make('gasto')
                ->label('Registrar Gasto')
                ->icon('heroicon-o-minus-circle')
                ->color('danger')
                ->form([
                    

                    Select::make('proveedor_id')
                        ->label('Proveedor')
                        ->options(
                            \App\Models\Proveedor::query()
                                ->where('activo', true)
                                ->orderBy('nombre')
                                ->pluck('nombre', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    TextInput::make('monto')
                        ->label('Monto')
                        ->numeric()
                        ->required(),

                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    TicketPago::create([
                        'ticket_id' => null,
                        'proveedor_id' => $data['proveedor_id'] ?? null,
                        'metodo_pago' => 'efectivo',
                        'monto' => $data['monto'],
                        'referencia' => $data['descripcion'],
                        'cancelado' => false,
                        'user_id' => auth()->id(),
                        'sucursal_id' => 1,
                        'tipo_movimiento' => 'gasto',
                        'categoria' => null,
                        'descripcion' => $data['descripcion'],
                    ]);

                    $this->cargarPagos();

                    Notification::make()
                        ->title('Gasto registrado')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function confirmarCerrarTurno(): void
    {
        $this->cerrarTurno();
    }

    public function cerrarTurno(): void
    {
        if (collect($this->pagos)->isEmpty()) {
            Notification::make()
                ->title('No hay movimientos para cerrar')
                ->warning()
                ->send();

            return;
        }

        DB::transaction(function () {
            $corte = CorteModel::create([
                'sucursal_id' => 1,
                'user_id' => auth()->id(),
                'fecha' => $this->fecha,
                'turno' => $this->turno,
                'total' => $this->resumen['saldo'] ?? 0,
                'total_efectivo' => (($this->resumen['efectivo'] ?? 0) + ($this->resumen['dotaciones'] ?? 0)) - ($this->resumen['gastos'] ?? 0),
                'total_tarjeta' => $this->resumen['tarjeta'] ?? 0,
                'total_transferencia' => $this->resumen['transferencia'] ?? 0,
                'cerrado_en' => now(),
            ]);

            TicketPago::whereIn('id', collect($this->pagos)->pluck('id')->toArray())
                ->update([
                    'corte_id' => $corte->id,
                ]);
        });

        $this->cargarPagos();

        Notification::make()
            ->title('Turno cerrado correctamente')
            ->success()
            ->send();
    }
}
