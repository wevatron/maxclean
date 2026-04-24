<?php

namespace App\Filament\Admin\Resources\Tickets\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('numero')
                    ->label('Ticket')
                    ->searchable()
                    ->weight('bold')
                    ->formatStateUsing(fn($state) => '#' . str_pad($state, 6, '0', STR_PAD_LEFT))
                    ->badge(fn($record) => $record->tipo === 'encargo_express')
                    ->color(
                        fn($record) =>
                        $record->tipo === 'encargo_express'
                            ? 'warning'
                            : null
                    )
                    ->icon(
                        fn($record) =>
                        $record->tipo === 'encargo_express'
                            ? 'heroicon-o-bolt'
                            : null
                    )
                    ->iconColor(
                        fn($record) =>
                        $record->tipo === 'encargo_express'
                            ? 'warning'
                            : null
                    ),

                TextColumn::make('tipo')
                    ->label('Modo')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'encargo_express' => 'Express',
                            'encargo_kilo' => 'Por kilo',
                            'encargo' => 'Por encargo',
                            default => ucfirst((string) $state),
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'encargo_express' => 'warning',
                            'encargo_kilo' => 'info',
                            'encargo' => 'success',
                            default => 'gray',
                        };
                    })
                    ->icon(function ($state) {
                        return match ($state) {
                            'encargo_express' => 'heroicon-o-bolt',
                            'encargo_kilo' => 'heroicon-o-scale',
                            'encargo' => 'heroicon-o-shopping-bag',
                            default => 'heroicon-o-question-mark-circle',
                        };
                    }),
                TextColumn::make('created_at')
                    ->label('Registro')
                    ->formatStateUsing(
                        fn($state) =>
                        Carbon::parse($state)->translatedFormat('d M y · H:i')
                    )
                    ->toggleable()
                    ->toggledHiddenByDefault(false),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->formatStateUsing(
                        fn($state) =>
                        Carbon::parse($state)->translatedFormat('d M y · H:i')
                    )
                    ->toggleable()
                    ->toggledHiddenByDefault(false),

                TextColumn::make('cliente.name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('total')
                    ->money('MXN'),

                TextColumn::make('pagado')
                    ->label('Pagado')
                    ->money('MXN')
                    ->toggleable()
                    ->toggledHiddenByDefault(true)
                    ->color('success'),

                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money('MXN')
                    ->color(
                        fn($record) =>
                        $record->saldo > 0 ? 'danger' : 'success'
                    ),

                BadgeColumn::make('status.nombre')
                    ->label('Estado')
                    ->toggleable()
                    ->toggledHiddenByDefault(true)
                    ->colors([
                        'gray'    => fn($record) => $record->saldo > 0,
                        'success' => fn($record) => $record->saldo <= 0,
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

                ViewAction::make()
                    ->label('Gestionar'),
            ]);
    }
}
