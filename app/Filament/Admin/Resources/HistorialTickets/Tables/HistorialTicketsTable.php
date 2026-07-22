<?php

namespace App\Filament\Admin\Resources\HistorialTickets\Tables;

use App\Filament\Admin\Resources\Cuentas\CuentaResource;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class HistorialTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->label('Ticket')
                    ->searchable()
                    ->weight('bold')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => '#' . str_pad((string) $state, 6, '0', STR_PAD_LEFT)),

                TextColumn::make('tipo')
                    ->label('Modo')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(function (?string $state) {
                        return match ($state) {
                            'encargo_express' => 'Express',
                            'encargo_kilo' => 'Por kilo',
                            'encargo' => 'Por encargo',
                            'autoservicio' => 'Autoservicio',
                            default => ucfirst((string) $state),
                        };
                    })
                    ->color(function (?string $state) {
                        return match ($state) {
                            'encargo_express' => 'warning',
                            'encargo_kilo' => 'success',
                            'encargo' => 'info',
                            'autoservicio' => 'gray',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('cliente.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cuenta.numero')
                    ->label('Cuenta')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('status.nombre')
                    ->label('Estatus')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (?string $state) => $state ?? 'Sin estatus')
                    ->color(function (?string $state) {
                        return match (mb_strtolower((string) $state)) {
                            'pagado' => 'success',
                            'parcial' => 'warning',
                            'abierta' => 'info',
                            'cancelada' => 'danger',
                            'entregado' => 'gray',
                            default => 'gray',
                        };
                    }),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('pagado')
                    ->label('Pagado')
                    ->state(fn (Ticket $record) => $record->pagos()->where('cancelado', false)->sum('monto'))
                    ->money('MXN')
                    ->color('success')
                    ->toggleable()
                    ->toggledHiddenByDefault(true),

                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->state(fn (Ticket $record) => $record->saldoPendiente())
                    ->money('MXN')
                    ->color(fn ($state) => (float) $state > 0 ? 'warning' : 'success'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->translatedFormat('d/m/Y H:i'))
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Filter::make('fecha')
                    ->label('Fecha')
                    ->form([
                        DatePicker::make('fecha')
                            ->label('Fecha del ticket')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['fecha'] ?? null,
                            fn ($query, $fecha) => $query->whereDate('created_at', $fecha)
                        );
                    }),

                Filter::make('periodo')
                    ->label('Periodo')
                    ->form([
                        Select::make('periodo')
                            ->label('Periodo')
                            ->options([
                                'today' => 'Hoy',
                                'last_7_days' => 'Últimos 7 días',
                                'this_month' => 'Este mes',
                                'last_month' => 'Mes pasado',
                            ])
                            ->placeholder('Todos'),
                    ])
                    ->query(function ($query, array $data) {
                        $periodo = $data['periodo'] ?? null;

                        return match ($periodo) {
                            'today' => $query->whereDate('created_at', Carbon::today()),
                            'last_7_days' => $query->whereBetween('created_at', [
                                Carbon::today()->subDays(6)->startOfDay(),
                                Carbon::today()->endOfDay(),
                            ]),
                            'this_month' => $query->whereBetween('created_at', [
                                Carbon::today()->startOfMonth(),
                                Carbon::today()->endOfMonth(),
                            ]),
                            'last_month' => $query->whereBetween('created_at', [
                                Carbon::today()->subMonthNoOverflow()->startOfMonth(),
                                Carbon::today()->subMonthNoOverflow()->endOfMonth(),
                            ]),
                            default => $query,
                        };
                    }),

                SelectFilter::make('tipo')
                    ->label('Modo')
                    ->options([
                        'encargo' => 'Por encargo',
                        'encargo_express' => 'Express',
                        'encargo_kilo' => 'Por kilo',
                        'autoservicio' => 'Autoservicio',
                    ]),

                SelectFilter::make('status_id')
                    ->relationship('status', 'nombre')
                    ->label('Estatus'),

                SelectFilter::make('sucursal_id')
                    ->relationship('sucursal', 'nombre')
                    ->label('Sucursal'),
            ])
            ->recordUrl(fn (Ticket $record): string => CuentaResource::getUrl('edit', [
                'record' => $record->cuenta,
            ]))
            ->recordActions([
                Action::make('imprimir')
                    ->label('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (Ticket $record): string => route('tickets.print', [
                        'ticket' => $record,
                    ])),

                Action::make('verCuenta')
                    ->label('Ver cuenta')
                    ->icon('heroicon-o-banknotes')
                    ->color('gray')
                    ->visible(fn (Ticket $record): bool => (bool) $record->cuenta_id && (bool) $record->cuenta)
                    ->url(fn (Ticket $record): string => CuentaResource::getUrl('edit', [
                        'record' => $record->cuenta,
                    ])),
            ]);
    }
}
