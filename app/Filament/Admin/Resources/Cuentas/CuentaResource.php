<?php

namespace App\Filament\Admin\Resources\Cuentas;

use App\Filament\Admin\Resources\Cuentas\Pages;
use App\Filament\Admin\Resources\Cuentas\RelationManagers\CuentaPagosRelationManager;
use App\Filament\Admin\Resources\Cuentas\RelationManagers\TicketPagosRelationManager;
use App\Filament\Admin\Resources\Cuentas\RelationManagers\TicketsRelationManager;
use App\Filament\Admin\Resources\Cuentas\Schemas\CuentaForm;
use App\Filament\Admin\Resources\Cuentas\Tables\CuentasTable;
use App\Models\Cuenta;
use App\Models\CuentaPago;
use App\Models\Punto;
use App\Models\Ticket;
use App\Models\TicketPago;
use App\Models\TicketStatus;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DBSchema;
use UnitEnum;

class CuentaResource extends Resource
{
    protected static ?string $model = Cuenta::class;

    protected static ?string $modelLabel = 'Cuenta';

    protected static ?string $pluralModelLabel = 'Cuentas';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión';
    protected static ?string $navigationLabel = 'F7 Cuentas';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function form(Schema $schema): Schema
    {
        return CuentaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CuentasTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            TicketsRelationManager::class,
            CuentaPagosRelationManager::class,
            TicketPagosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCuentas::route('/'),
            'edit' => Pages\EditCuenta::route('/{record}/edit'),
        ];
    }

    public static function recalcularCuenta(Cuenta $cuenta): void
    {
        $cuenta->refresh();

        if ($cuenta->estatus === 'cancelada') {
            return;
        }

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

    public static function liquidarCuenta(Cuenta $cuenta, array $data): void
    {
        try {
            DB::transaction(function () use ($cuenta, $data) {
                $cuenta = Cuenta::query()
                    ->whereKey($cuenta->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($cuenta->estatus === 'cancelada') {
                    throw new \Exception('No se puede abonar o liquidar una cuenta cancelada.');
                }

                self::recalcularCuenta($cuenta);

                $cuenta->refresh();

                $saldoCuenta = round((float) $cuenta->saldo, 2);

                if ($saldoCuenta <= 0) {
                    Notification::make()
                        ->title('La cuenta ya está liquidada')
                        ->warning()
                        ->send();

                    return;
                }

                $montoDisponible = round((float) ($data['monto'] ?? 0), 2);

                if ($montoDisponible <= 0) {
                    throw new \Exception('El monto a abonar debe ser mayor a 0.');
                }

                if ($montoDisponible > $saldoCuenta) {
                    throw new \Exception('El monto a abonar no puede ser mayor al saldo pendiente.');
                }

                $metodoPago = $data['metodo_pago'] ?? 'efectivo';
                $referencia = $data['referencia'] ?? null;

                $cuentaPago = CuentaPago::create([
                    'cuenta_id' => $cuenta->id,
                    'cliente_id' => $cuenta->cliente_id,
                    'sucursal_id' => $cuenta->sucursal_id,
                    'user_id' => auth()->id(),
                    'monto' => $montoDisponible,
                    'metodo_pago' => $metodoPago,
                    'referencia' => $referencia,
                    'cancelado' => false,
                    'notas' => $montoDisponible >= $saldoCuenta
                        ? 'Liquidación completa de cuenta ' . $cuenta->numero
                        : 'Abono parcial a cuenta ' . $cuenta->numero,
                ]);

                $restante = $montoDisponible;

                $tickets = Ticket::query()
                    ->where('cuenta_id', $cuenta->id)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                $statusPagadoId = TicketStatus::whereRaw('LOWER(nombre) = ?', ['pagado'])->value('id');

                foreach ($tickets as $ticket) {
                    if ($restante <= 0) {
                        break;
                    }

                    $pagadoTicket = $ticket->pagos()
                        ->where('cancelado', false)
                        ->sum('monto');

                    $saldoTicket = round(max((float) $ticket->total - (float) $pagadoTicket, 0), 2);

                    if ($saldoTicket <= 0) {
                        if ($statusPagadoId) {
                            $ticket->update([
                                'status_id' => $statusPagadoId,
                            ]);
                        }

                        continue;
                    }

                    $montoAplicado = min($saldoTicket, $restante);
                    $montoAplicado = round($montoAplicado, 2);

                    $pago = $ticket->pagos()->create([
                        'metodo_pago' => $metodoPago,
                        'monto' => $montoAplicado,
                        'referencia' => $referencia,
                        'user_id' => auth()->id(),
                        'sucursal_id' => $cuenta->sucursal_id,
                        'cancelado' => false,
                        'tipo_movimiento' => 'venta',
                    ]);

                    $pago->forceFill([
                        'cuenta_id' => $cuenta->id,
                        'cuenta_pago_id' => $cuentaPago->id,
                    ])->save();

                    Punto::create([
                        'user_id' => $cuenta->cliente_id,
                        'asignado_por' => auth()->id(),
                        'puntos' => (int) round($montoAplicado),
                        'fecha' => now(),
                        'tikete' => $ticket->numero,
                        'sucursal_id' => $cuenta->sucursal_id,
                    ]);

                    $restante = round($restante - $montoAplicado, 2);

                    $nuevoPagadoTicket = $ticket->pagos()
                        ->where('cancelado', false)
                        ->sum('monto');

                    $nuevoSaldoTicket = round(max((float) $ticket->total - (float) $nuevoPagadoTicket, 0), 2);

                    if ($nuevoSaldoTicket <= 0 && $statusPagadoId) {
                        $ticket->update([
                            'status_id' => $statusPagadoId,
                        ]);
                    }
                }

                self::recalcularCuenta($cuenta);
            });

            $cuenta->refresh();

            Notification::make()
                ->title($cuenta->saldo <= 0 ? 'Cuenta liquidada correctamente' : 'Abono registrado correctamente')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error al registrar abono')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function cancelarCuenta(Cuenta $cuenta, array $data): void
    {
        try {
            DB::transaction(function () use ($cuenta, $data) {
                $cuenta = Cuenta::query()
                    ->whereKey($cuenta->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($cuenta->estatus === 'cancelada') {
                    Notification::make()
                        ->title('La cuenta ya está cancelada')
                        ->warning()
                        ->send();

                    return;
                }

                $ticketIds = Ticket::query()
                    ->where('cuenta_id', $cuenta->id)
                    ->pluck('id');

                if ($ticketIds->isEmpty()) {
                    $cuenta->forceFill([
                        'total_pagado' => 0,
                        'saldo' => 0,
                        'estatus' => 'cancelada',
                        'cerrada_en' => now(),
                        'notas' => trim(
                            ($cuenta->notas ? $cuenta->notas . "\n\n" : '') .
                            'Cuenta cancelada el ' . now()->format('d/m/Y H:i') .
                            ' por ' . (auth()->user()?->name ?? 'Usuario') .
                            '. Motivo: ' . (($data['motivo'] ?? null) ?: 'Sin motivo')
                        ),
                    ])->save();

                    return;
                }

                if (DBSchema::hasColumn('ticket_pagos', 'corte_id')) {
                    $pagosEnCorte = TicketPago::query()
                        ->whereIn('ticket_id', $ticketIds)
                        ->whereNotNull('corte_id')
                        ->where('cancelado', false)
                        ->exists();

                    if ($pagosEnCorte) {
                        throw new \Exception('No se puede cancelar esta cuenta porque tiene pagos incluidos en un corte de caja.');
                    }
                }

                $statusCanceladoId = TicketStatus::query()
                    ->whereRaw('LOWER(nombre) = ?', ['cancelado'])
                    ->value('id');

                $tickets = Ticket::query()
                    ->where('cuenta_id', $cuenta->id)
                    ->lockForUpdate()
                    ->get();

                foreach ($tickets as $ticket) {
                    Punto::query()
                        ->where('user_id', $cuenta->cliente_id)
                        ->where('sucursal_id', $cuenta->sucursal_id)
                        ->where('tikete', $ticket->numero)
                        ->delete();

                    if ($statusCanceladoId) {
                        $ticket->update([
                            'status_id' => $statusCanceladoId,
                        ]);
                    }
                }

                $ticketPagoUpdate = [
                    'cancelado' => true,
                    'updated_at' => now(),
                ];

                if (DBSchema::hasColumn('ticket_pagos', 'cancelado_en')) {
                    $ticketPagoUpdate['cancelado_en'] = now();
                }

                if (DBSchema::hasColumn('ticket_pagos', 'cancelado_por')) {
                    $ticketPagoUpdate['cancelado_por'] = auth()->id();
                }

                TicketPago::query()
                    ->whereIn('ticket_id', $ticketIds)
                    ->where('cancelado', false)
                    ->update($ticketPagoUpdate);

                $cuentaPagoUpdate = [
                    'cancelado' => true,
                    'updated_at' => now(),
                ];

                if (DBSchema::hasColumn('cuenta_pagos', 'cancelado_en')) {
                    $cuentaPagoUpdate['cancelado_en'] = now();
                }

                if (DBSchema::hasColumn('cuenta_pagos', 'cancelado_por')) {
                    $cuentaPagoUpdate['cancelado_por'] = auth()->id();
                }

                CuentaPago::query()
                    ->where('cuenta_id', $cuenta->id)
                    ->where('cancelado', false)
                    ->update($cuentaPagoUpdate);

                $motivo = trim((string) ($data['motivo'] ?? ''));

                $cuenta->forceFill([
                    'total_pagado' => 0,
                    'saldo' => 0,
                    'estatus' => 'cancelada',
                    'cerrada_en' => now(),
                    'notas' => trim(
                        ($cuenta->notas ? $cuenta->notas . "\n\n" : '') .
                        'Cuenta cancelada el ' . now()->format('d/m/Y H:i') .
                        ' por ' . (auth()->user()?->name ?? 'Usuario') .
                        '. Motivo: ' . ($motivo ?: 'Sin motivo')
                    ),
                ])->save();
            });

            Notification::make()
                ->title('Cuenta cancelada correctamente')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error al cancelar cuenta')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
