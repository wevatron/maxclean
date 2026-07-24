<?php

namespace App\Filament\Admin\Resources\Cuentas\RelationManagers;

use App\Models\TicketPago;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Attributes\On;

class TicketPagosRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketPagos';

    protected static ?string $title = 'Pagos aplicados a tickets';

    #[On('refresh-cuenta-relation-managers')]
    public function refreshRelationManagerTable(): void
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket')
                    ->label('Ticket')
                    ->state(function (TicketPago $record): string {
                        if (method_exists($record, 'ticket')) {
                            return $record->ticket?->numero ?? 'S/I';
                        }

                        return (string) ($record->ticket_id ?? 'S/I');
                    })
                    ->searchable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2))
                    ->color('success')
                    ->sortable(),

                TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => ucfirst((string) $state)),

                TextColumn::make('tipo_movimiento')
                    ->label('Movimiento')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => ucfirst((string) $state)),

                TextColumn::make('referencia')
                    ->label('Referencia')
                    ->placeholder('—')
                    ->searchable(),

                IconColumn::make('cancelado')
                    ->label('Cancelado')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
