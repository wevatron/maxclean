<?php

namespace App\Filament\Admin\Resources\Cuentas\Pages;

use App\Filament\Admin\Resources\Cuentas\CuentaResource;
use App\Models\Cuenta;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class EditCuenta extends EditRecord
{
    protected static string $resource = CuentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('imprimirCuenta')
                ->label('Imprimir cuenta')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn() => route('cuentas.ticket', $this->record))
                ->openUrlInNewTab(),

            Action::make('terminarTickets')
                ->label('Terminar tickets')
                ->icon('heroicon-o-check-circle')
                ->color('warning')
                ->visible(fn (): bool => $this->ticketsLiquidables()->isNotEmpty())
                ->modalHeading(fn () => 'Terminar tickets de la cuenta ' . $this->record->numero)
                ->modalDescription('Selecciona los tickets liquidados que quieres marcar como entregados. Se completarán todos sus procesos.')
                ->modalWidth('lg')
                ->form([
                    CheckboxList::make('tickets')
                        ->label('Tickets liquidados')
                        ->options(fn () => $this->ticketsLiquidablesForSelect())
                        ->columns(1)
                        ->required()
                        ->helperText('Solo se muestran tickets con saldo en cero.'),
                ])
                ->action(function (array $data) {
                    $ticketIds = array_values(array_filter($data['tickets'] ?? []));

                    if ($ticketIds === []) {
                        Notification::make()
                            ->title('Selecciona al menos un ticket')
                            ->danger()
                            ->send();

                        return;
                    }

                    $tickets = $this->record->tickets()
                        ->whereIn('id', $ticketIds)
                        ->with(['procesos', 'pagos'])
                        ->get()
                        ->filter(fn (Ticket $ticket) => $this->esTicketLiquidable($ticket))
                        ->values();

                    if ($tickets->isEmpty()) {
                        Notification::make()
                            ->title('No hay tickets válidos para terminar')
                            ->body('Solo puedes terminar tickets liquidados.')
                            ->danger()
                            ->send();

                        return;
                    }

                    DB::transaction(function () use ($tickets): void {
                        foreach ($tickets as $ticket) {
                            $this->terminarTicketConProcesos($ticket);
                        }
                    });

                    $this->record->refresh();
                    $this->refreshCuentaRelationManagers();

                    Notification::make()
                        ->title($tickets->count() === 1 ? 'Ticket terminado' : 'Tickets terminados')
                        ->success()
                        ->body($tickets->count() === 1
                            ? 'Se completaron todos sus procesos y se marcó como entregado.'
                            : 'Se completaron todos sus procesos y se marcaron como entregados.')
                        ->send();
                }),

            Action::make('recalcular')
                ->label('Recalcular cuenta')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->visible(fn(): bool => $this->record->estatus !== 'cancelada')
                ->action(function () {
                    /** @var Cuenta $record */
                    $record = $this->record;

                    CuentaResource::recalcularCuenta($record);

                    $record->refresh();
                    $this->refreshCuentaRelationManagers();

                    $this->refreshFormData([
                        'total',
                        'total_pagado',
                        'saldo',
                        'estatus',
                        'cerrada_en',
                    ]);
                }),

            Action::make('abonarCuenta')
                ->label('Abonar / Liquidar cuenta')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn(): bool => (float) $this->record->saldo > 0 && in_array($this->record->estatus, ['abierta', 'parcial'], true))
                ->requiresConfirmation()
                ->modalHeading(fn() => 'Abonar a cuenta ' . $this->record->numero)
                ->modalDescription('Puedes abonar una parcialidad o liquidar el saldo completo. El pago se repartirá entre los tickets pendientes.')
                ->modalContent(fn() => $this->descuentoCuentaBadge())
                ->form([
                    TextInput::make('monto')
                        ->label('Monto a abonar')
                        ->prefix('$')
                        ->numeric()
                        ->minValue(fn() => (float) $this->record->saldo > 0 ? 0.01 : 0)
                        ->default(fn() => (float) $this->record->saldo)
                        ->required()
                        ->live(onBlur: true)
                        ->helperText(fn() => $this->helperSaldoCuenta()),

                    TextInput::make('efectivo_recibido')
                        ->label('Efectivo recibido')
                        ->prefix('$')
                        ->numeric()
                        ->default(fn (Get $get) => (float) ($get('monto') ?? $this->record->saldo))
                        ->live(onBlur: true)
                        ->visible(fn (Get $get) => ($get('metodo_pago') ?? 'efectivo') === 'efectivo')
                        ->helperText(function (Get $get): string {
                            return $this->helperCambioCuenta($get);
                        }),

                    TextInput::make('referencia')
                        ->label('Referencia')
                        ->placeholder('Número de transferencia, autorización o nota')
                        ->maxLength(255)
                        ->visible(fn (Get $get) => ($get('metodo_pago') ?? 'efectivo') === 'transferencia')
                        ->helperText('Opcional. Úsala para identificar la transferencia o un comprobante.'),

                    Select::make('metodo_pago')
                        ->label('Método de pago')
                        ->options([
                            'efectivo' => 'Efectivo',
                            'transferencia' => 'Transferencia',
                            'tarjeta' => 'Tarjeta',
                        ])
                        ->default('efectivo')
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            if ($state === 'transferencia') {
                                $set('efectivo_recibido', null);
                            } else {
                                $set('referencia', null);
                                if ($state !== 'efectivo') {
                                    $set('efectivo_recibido', null);
                                }
                            }
                        })
                        ->required(),
                ])
                ->action(function (array $data) {
                    /** @var Cuenta $record */
                    $record = $this->record;

                    CuentaResource::liquidarCuenta($record, $data);

                    $record->refresh();
                    $this->refreshCuentaRelationManagers();

                    $this->refreshFormData([
                        'total',
                        'total_pagado',
                        'saldo',
                        'estatus',
                        'cerrada_en',
                    ]);
                }),

            Action::make('cancelarCuenta')
                ->label('Cancelar cuenta')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(): bool => $this->record->estatus !== 'cancelada')
                ->requiresConfirmation()
                ->modalHeading(fn() => 'Cancelar cuenta ' . $this->record->numero)
                ->modalDescription('Esta acción cancelará la cuenta, sus tickets, sus pagos y eliminará los puntos generados por esos tickets. No se permitirá si algún pago ya está incluido en un corte de caja.')
                ->form([
                    Textarea::make('motivo')
                        ->label('Motivo de cancelación')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->action(function (array $data) {
                    /** @var Cuenta $record */
                    $record = $this->record;

                    CuentaResource::cancelarCuenta($record, $data);

                    $record->refresh();
                    $this->refreshCuentaRelationManagers();

                    $this->refreshFormData([
                        'total',
                        'total_pagado',
                        'saldo',
                        'estatus',
                        'cerrada_en',
                        'notas',
                    ]);
                }),
        ];
    }

    protected function refreshCuentaRelationManagers(): void
    {
        $this->dispatch('refresh-cuenta-relation-managers');
    }

    protected function ticketsLiquidables()
    {
        $this->record->loadMissing([
            'tickets.procesos',
            'tickets.pagos',
        ]);

        return $this->record->tickets->filter(fn (Ticket $ticket) => $this->esTicketLiquidable($ticket));
    }

    protected function ticketsLiquidablesForSelect(): array
    {
        return $this->ticketsLiquidables()
            ->mapWithKeys(fn (Ticket $ticket) => [
                $ticket->id => $this->labelTicketLiquidable($ticket),
            ])
            ->all();
    }

    protected function labelTicketLiquidable(Ticket $ticket): string
    {
        $tipoTicket = match ($ticket->tipo) {
            'encargo' => 'Pieza',
            'encargo_express' => 'Express',
            'encargo_kilo' => 'Kilo',
            'autoservicio' => 'Auto',
            default => (string) $ticket->tipo,
        };

        return sprintf(
            '#%s · %s · %s',
            str_pad((string) $ticket->numero, 6, '0', STR_PAD_LEFT),
            $tipoTicket,
            '$' . number_format((float) $ticket->total, 2)
        );
    }

    protected function esTicketLiquidable(Ticket $ticket): bool
    {
        return (float) $ticket->saldo <= 0;
    }

    protected function terminarTicketConProcesos(Ticket $ticket): void
    {
        $ticket->loadMissing('procesos');

        foreach (Ticket::ordenProcesos() as $nombreProceso) {
            $ticket->procesos()->updateOrCreate(
                ['proceso' => $nombreProceso],
                ['completado' => true],
            );
        }

        $ticket->procesos()->update([
            'completado' => true,
        ]);

        $statusEntregadoId = \App\Models\TicketStatus::query()
            ->whereRaw('LOWER(nombre) = ?', ['entregado'])
            ->value('id');

        $ticket->update([
            'status_id' => $statusEntregadoId ?? 5,
        ]);
    }

    protected function descuentoCuentaBadge(): ?HtmlString
    {
        $descuentoGuardado = (float) ($this->record->descuento_aplicado ?? 0);

        if ($descuentoGuardado > 0) {
            return new HtmlString('
                <div style="
                    margin-bottom:16px;
                    padding:12px 14px;
                    border-radius:14px;
                    border:1px solid #bfdbfe;
                    background:#eff6ff;
                    color:#1d4ed8;
                    font-weight:700;
                ">
                    Descuento guardado: $' . number_format($descuentoGuardado, 2) . '
                </div>
            ');
        }

        return null;
    }

    protected function helperSaldoCuenta(): string
    {
        $saldo = (float) $this->record->saldo;
        $descuento = (float) ($this->record->descuento_aplicado ?? 0);

        if ($descuento > 0) {
            return 'Saldo pendiente: $' . number_format($saldo, 2) . ' · Descuento guardado: $' . number_format($descuento, 2);
        }

        return 'Saldo pendiente: $' . number_format($saldo, 2);
    }

    protected function helperCambioCuenta(Get $get): string
    {
        $monto = (float) ($get('monto') ?? 0);
        $efectivoRecibido = (float) ($get('efectivo_recibido') ?? 0);

        if ($monto <= 0) {
            return 'Captura primero el monto a abonar.';
        }

        if ($efectivoRecibido <= 0) {
            return 'Captura cuánto te entrega el cliente para calcular el cambio.';
        }

        if ($efectivoRecibido >= $monto) {
            return 'Cambio a devolver: $' . number_format($efectivoRecibido - $monto, 2);
        }

        return 'Faltan $' . number_format($monto - $efectivoRecibido, 2) . ' para cubrir el pago.';
    }

    protected function buildCuentaPrinterPayload(Cuenta $cuenta): array
    {
        $tickets = $cuenta->tickets;
        $pagosAplicados = $cuenta->ticketPagos;

        $totalTickets = (float) $tickets->sum('total');
        $totalDescuentos = (float) $tickets->sum(fn ($ticket) => (float) ($ticket->descuento_aplicado ?? 0));
        $totalAntesDescuento = $totalTickets + $totalDescuentos;
        $totalPagado = (float) $pagosAplicados->sum('monto');
        $saldo = max($totalTickets - $totalPagado, 0);

        return [
            'tipo' => 'cuenta',
            'titulo' => 'MAX & CLEAN',
            'subtitulo' => 'Estado de cuenta',
            'sucursal' => $cuenta->sucursal?->nombre,
            'fecha' => now()->format('d/m/Y H:i'),
            'numero' => $cuenta->numero,
            'total' => $totalTickets,
            'qr' => (string) $cuenta->id,
            'numero_impreso' => str_pad((string) $cuenta->id, 6, '0', STR_PAD_LEFT),
            'cuenta' => [
                'id' => $cuenta->id,
                'numero' => $cuenta->numero,
                'cliente' => $cuenta->cliente?->name,
                'whatsapp' => $cuenta->cliente?->whatsapp,
                'estatus' => ucfirst((string) $cuenta->estatus),
                'abierta_en' => $cuenta->abierta_en?->format('d/m/Y H:i')
                    ?? $cuenta->created_at?->format('d/m/Y H:i'),
                'notas' => $cuenta->notas,
            ],
            'tickets' => $tickets->map(function ($ticket) {
                $pagadoTicket = (float) $ticket->pagos->where('cancelado', false)->sum('monto');
                $saldoTicket = max((float) $ticket->total - $pagadoTicket, 0);

                return [
                    'numero' => $ticket->numero,
                    'tipo' => match ($ticket->tipo) {
                        'encargo' => 'Pieza',
                        'encargo_express' => 'Express',
                        'encargo_kilo' => 'Kilo',
                        'autoservicio' => 'Auto',
                        default => (string) $ticket->tipo,
                    },
                    'total' => number_format((float) $ticket->total, 2, '.', ''),
                    'descuento' => number_format((float) ($ticket->descuento_aplicado ?? 0), 2, '.', ''),
                    'pagado' => number_format($pagadoTicket, 2, '.', ''),
                    'saldo' => number_format($saldoTicket, 2, '.', ''),
                ];
            })->values()->all(),
            'items' => $tickets->map(function ($ticket) {
                $pagadoTicket = (float) $ticket->pagos->where('cancelado', false)->sum('monto');
                $saldoTicket = max((float) $ticket->total - $pagadoTicket, 0);

                return [
                    'nombre' => 'Ticket #' . $ticket->numero . ' (' . match ($ticket->tipo) {
                        'encargo' => 'Pieza',
                        'encargo_express' => 'Express',
                        'encargo_kilo' => 'Kilo',
                        'autoservicio' => 'Auto',
                        default => (string) $ticket->tipo,
                    } . ')',
                    'precio' => number_format((float) $ticket->total, 2, '.', ''),
                    'descuento' => number_format((float) ($ticket->descuento_aplicado ?? 0), 2, '.', ''),
                    'pagado' => number_format($pagadoTicket, 2, '.', ''),
                    'saldo' => number_format($saldoTicket, 2, '.', ''),
                ];
            })->values()->all(),
            'pagos_aplicados' => $pagosAplicados->map(function ($pago) {
                return [
                    'fecha' => $pago->created_at?->format('d/m H:i'),
                    'ticket' => $pago->ticket?->numero ?? 'S/I',
                    'metodo_pago' => ucfirst((string) $pago->metodo_pago),
                    'monto' => number_format((float) $pago->monto, 2, '.', ''),
                ];
            })->values()->all(),
            'resumen' => [
                'total_antes_descuento' => number_format($totalAntesDescuento, 2, '.', ''),
                'descuentos_aplicados' => number_format($totalDescuentos, 2, '.', ''),
                'total_tickets' => number_format($totalTickets, 2, '.', ''),
                'total_pagado' => number_format($totalPagado, 2, '.', ''),
                'saldo' => number_format($saldo, 2, '.', ''),
            ],
        ];
    }

    protected function printerServiceUrl(): string
    {
        $url = config('services.printer.url');

        if (is_string($url) && trim($url) !== '') {
            return trim($url);
        }

        return env('PRINTER_SERVICE_URL', 'http://192.168.1.114:5000/print');
    }

    protected function printerTimeout(): int
    {
        $timeout = config('services.printer.timeout');

        if (is_numeric($timeout) && (int) $timeout > 0) {
            return (int) $timeout;
        }

        return (int) env('PRINTER_SERVICE_TIMEOUT', 10);
    }
}
