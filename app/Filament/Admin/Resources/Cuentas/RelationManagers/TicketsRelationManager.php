<?php

namespace App\Filament\Admin\Resources\Cuentas\RelationManagers;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $title = 'Tickets incluidos';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'encargo' => 'Por pieza',
                        'encargo_express' => 'Express',
                        'encargo_kilo' => 'Por kilo',
                        'autoservicio' => 'Autoservicio',
                        default => $state ?? 'Sin tipo',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'encargo' => 'info',
                        'encargo_express' => 'warning',
                        'encargo_kilo' => 'success',
                        'autoservicio' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('estatus')
                    ->label('Estatus')
                    ->state(function (Ticket $record): string {
                        if (method_exists($record, 'status')) {
                            return $record->status?->nombre ?? 'S/I';
                        }

                        return (string) ($record->status_id ?? 'S/I');
                    })
                    ->badge(),

                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2))
                    ->sortable(),

                TextColumn::make('descuento')
                    ->label('Descuento')
                    ->state(fn (Ticket $record): float => (float) ($record->descuento_aplicado ?? 0))
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2))
                    ->color('warning'),

                TextColumn::make('pagado')
                    ->label('Pagado')
                    ->state(fn (Ticket $record) => $record->pagos()->where('cancelado', false)->sum('monto'))
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2))
                    ->color('success'),

                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->state(function (Ticket $record): float {
                        $pagado = $record->pagos()->where('cancelado', false)->sum('monto');

                        return max((float) $record->total - (float) $pagado, 0);
                    })
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2))
                    ->color(fn ($state) => (float) $state > 0 ? 'warning' : 'success'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([])
            ->recordActions([
                Action::make('verTicket')
                    ->label('Ver ticket')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Ticket $record): string => TicketResource::getUrl('view', [
                        'record' => $record,
                    ])),
            ])
            ->toolbarActions([]);
    }
}
