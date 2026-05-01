<?php

namespace App\Filament\Cliente\Resources\Tickets\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'default' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    Grid::make(2)
                        ->schema([
                            TextColumn::make('numero')
                                ->label('Ticket')
                                ->formatStateUsing(fn ($state) => 'Ticket #' . str_pad($state, 6, '0', STR_PAD_LEFT))
                                ->weight('bold')
                                ->size('lg')
                                ->color('primary'),

                            TextColumn::make('status.nombre')
                                ->label('Estado')
                                ->badge()
                                ->alignEnd()
                                ->color(
                                    fn ($record) =>
                                    $record->saldo > 0 ? 'warning' : 'success'
                                ),
                        ]),

                    TextColumn::make('created_at')
                        ->label('Fecha')
                        ->formatStateUsing(
                            fn ($state) =>
                            Carbon::parse($state)->translatedFormat('d M y · H:i')
                        )
                        ->color('gray')
                        ->icon('heroicon-o-calendar-days'),

                    Grid::make(3)
                        ->schema([
                            TextColumn::make('total')
                                ->label('Total')
                                ->money('MXN')
                                ->weight('bold'),

                            TextColumn::make('pagado')
                                ->label('Pagado')
                                ->money('MXN')
                                ->color('success')
                                ->weight('bold'),

                            TextColumn::make('saldo')
                                ->label('Saldo')
                                ->money('MXN')
                                ->color(
                                    fn ($record) =>
                                    $record->saldo > 0 ? 'danger' : 'success'
                                )
                                ->weight('bold'),
                        ]),

                    TextColumn::make('tipo')
                        ->label('Tipo')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'encargo_express' => 'Express',
                            'encargo_kilo' => 'Por kilo',
                            'autoservicio' => 'Autoservicio',
                            default => 'Encargo',
                        })
                        ->color(fn ($state) => match ($state) {
                            'encargo_express' => 'warning',
                            'encargo_kilo' => 'success',
                            'autoservicio' => 'info',
                            default => 'gray',
                        }),
                ])
                    ->space(3),
            ])
            ->searchable(false)
            ->defaultSort('id', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->label('Ver detalle')
                    ->icon('heroicon-o-eye')
                    ->button(),
            ])
            ->toolbarActions([]);
    }
}