<?php

namespace App\Filament\Admin\Resources\Tickets\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('numero')
                    ->label('Ticket')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('operador.name')
                    ->label('Operador')
                    ->searchable(),

                TextColumn::make('total')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('pagado')
                    ->label('Pagado')
                    ->money('MXN')
                    ->color('success'),

                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money('MXN')
                    ->color(fn ($record) =>
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
            ->recordAction('view')
            ->filters([
                SelectFilter::make('status_id')
                    ->relationship('status', 'nombre')
                    ->label('Estado'),
            ])

            ->recordActions([

                ViewAction::make(),

                Action::make('registrarPago')
                    ->label('Registrar pago')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->visible(fn ($record) => $record->saldo > 0)
                    ->action(function ($record) {
                        // después aquí abrimos modal
                    }),
            ]);
    }
}