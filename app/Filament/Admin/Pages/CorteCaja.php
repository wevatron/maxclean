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

class CorteCaja extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;
public $fechaBusqueda;
public $corteSeleccionado;
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

protected function getHeaderActions(): array
{
    return [

        Action::make('descargar_corte_por_fecha')
            ->label('Descargar Corte')
            ->icon('heroicon-o-document-arrow-down')
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
                            ->mapWithKeys(fn ($corte) => [
                                $corte->id =>
                                    'Turno: ' .
                                    ucfirst($corte->turno) .
                                    ' | Total: $' .
                                    number_format($corte->total, 2)
                            ]);
                    })
                    ->searchable()
                    ->required(),

            ])
            ->action(function (array $data) {

                $corte = CorteModel::find($data['corte_id']);

                if (! $corte) {
                    return;
                }

                $corte->load('pagos', 'sucursal', 'operador');

                return response()->streamDownload(function () use ($corte) {
                    echo Pdf::loadView('pdf.corte-caja', [
                        'corte' => $corte,
                    ])->output();
                }, 'corte-'.$corte->id.'.pdf');
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
        if (empty($this->pagos)) {
            return;
        }

        DB::transaction(function () {

            $sucursalId = collect($this->pagos)->pluck('sucursal_id')->unique()->first();

            $corte = CorteModel::create([
                'sucursal_id' => $sucursalId,
                'user_id' => auth()->id(),
                'fecha' => $this->fecha,
                'turno' => $this->turno,
                'total' => $this->resumen['total'],
                'total_efectivo' => $this->resumen['efectivo'],
                'total_tarjeta' => $this->resumen['tarjeta'],
                'total_transferencia' => $this->resumen['transferencia'],
                'cerrado_en' => now(),
            ]);

            TicketPago::whereIn('id', collect($this->pagos)->pluck('id'))
                ->update([
                    'corte_id' => $corte->id
                ]);
        });

        $this->cargarPagos();
    }
}
