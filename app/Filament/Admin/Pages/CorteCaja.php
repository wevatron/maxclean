<?php

namespace App\Filament\Admin\Pages;

use App\Models\TicketPago;
use App\Models\CorteCaja as CorteModel;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class CorteCaja extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;
    public $fechaBusqueda;
    public $corteSeleccionado;
    protected string $view = 'filament.admin.pages.corte-caja';

    public $fecha;
    public $turno = 'matutino';
    protected $listeners = [
        'ejecutarCierreTurno' => 'cerrarTurno',
    ];

    public $resumen = [];
    public $pagos = [];

    public function mount(): void
    {
        $this->fecha = now()->toDateString();
        $this->cargarPagos();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:CorteCaja');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('View:CorteCaja');
    }

    public function confirmarCerrarTurno()
    {
        Notification::make()
            ->title('¿Cerrar este turno?')
            ->body('Esta acción cerrará el corte con los pagos pendientes del turno seleccionado.')
            ->warning()
            ->actions([
                Action::make('confirmar')
                    ->label('Sí, cerrar turno')
                    ->color('success')
                    ->button()
                    ->dispatch('ejecutarCierreTurno')
                    ->close(),

                Action::make('cancelar')
                    ->label('Cancelar')
                    ->color('gray')
                    ->button()
                    ->close(),
            ])
            ->send();
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
                    ->orderBy('turno')
                    ->get()
                    ->mapWithKeys(fn($corte) => [
                        $corte->id =>
                            'Turno: ' .
                            ucfirst($corte->turno) .
                            ' | Total: $' .
                            number_format($corte->total, 2),
                    ]);
            })
            ->searchable()
            ->required(),
    ])
    ->action(function (array $data) {
        $url = route('cortes-caja.pdf', [
            'corte' => $data['corte_id'],
        ]);

        $this->js("window.open('{$url}', '_blank')");
    }),

        ];
    }

    public function cargarPagos(): void
    {
        $this->pagos = TicketPago::query()
            ->whereNull('corte_id')
            ->where('cancelado', false)
            ->whereDate('created_at', $this->fecha)
            ->where('user_id', auth()->id())
            ->get();

        $this->resumen = [
            'total' => collect($this->pagos)->sum('monto'),
            'efectivo' => collect($this->pagos)->where('metodo_pago', 'efectivo')->sum('monto'),
            'tarjeta' => collect($this->pagos)->where('metodo_pago', 'tarjeta')->sum('monto'),
            'transferencia' => collect($this->pagos)->where('metodo_pago', 'transferencia')->sum('monto'),
        ];
    }

    public function cerrarTurno(): void
    {
        $this->cargarPagos();

        if ($this->pagos->isEmpty()) {
            Notification::make()
                ->title('No hay pagos para cerrar')
                ->body('No existen pagos pendientes para este turno.')
                ->warning()
                ->send();

            return;
        }

        $sucursalId = collect($this->pagos)
            ->pluck('sucursal_id')
            ->filter()
            ->unique()
            ->first();

        if (! $sucursalId) {
            Notification::make()
                ->title('No se puede cerrar el turno')
                ->body('Los pagos encontrados no tienen sucursal asignada.')
                ->danger()
                ->send();

            return;
        }

        DB::transaction(function () use ($sucursalId) {
            $corte = CorteModel::create([
                'sucursal_id' => $sucursalId,
                'user_id' => auth()->id(),
                'fecha' => $this->fecha,
                'turno' => $this->turno,
                'total' => $this->resumen['total'] ?? 0,
                'total_efectivo' => $this->resumen['efectivo'] ?? 0,
                'total_tarjeta' => $this->resumen['tarjeta'] ?? 0,
                'total_transferencia' => $this->resumen['transferencia'] ?? 0,
                'cerrado_en' => now(),
            ]);

            TicketPago::whereIn('id', collect($this->pagos)->pluck('id'))
                ->update([
                    'corte_id' => $corte->id,
                ]);
        });

        Notification::make()
            ->title('Turno cerrado correctamente')
            ->success()
            ->send();

        $this->cargarPagos();
    }
}
