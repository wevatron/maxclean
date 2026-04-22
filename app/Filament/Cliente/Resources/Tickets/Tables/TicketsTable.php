<?php

namespace App\Filament\Cliente\Resources\Tickets\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\ViewAction;
use Illuminate\Support\Carbon;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('numero')
                    ->label('Ticket')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->formatStateUsing(
                        fn ($state) =>
                        Carbon::parse($state)->translatedFormat('d M y · H:i')
                    )
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN'),

                TextColumn::make('pagado')
                    ->label('Pagado')
                    ->money('MXN')
                    ->color('success'),

                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money('MXN')
                    ->color(
                        fn ($record) =>
                            $record->saldo > 0 ? 'danger' : 'success'
                    ),

                BadgeColumn::make('status.nombre')
                    ->label('Estado')
                    ->colors([
                        'gray'    => fn ($record) => $record->saldo > 0,
                        'success' => fn ($record) => $record->saldo <= 0,
                    ]),
            ])

            ->defaultSort('id', 'desc')

            // Solo permitir ver
            ->recordActions([
                ViewAction::make()
                    ->label('Ver detalle'),
            ])

            // Sin acciones masivas
            ->toolbarActions([]);
    }
}