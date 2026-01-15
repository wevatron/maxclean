<?php

namespace App\Filament\Cliente\Resources\Puntos\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;

class PuntosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->recordUrl(null) 
            ->columns([

                Stack::make([

                    // 🔢 PUNTOS (lo más importante)
                    TextColumn::make('puntos')
                        ->label('')
                        ->formatStateUsing(fn ($state) => ($state > 0 ? '+' : '') . $state . ' puntos')
                        ->size('lg')
                        ->weight('bold')
                        ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                    // 🏬 Sucursal
                    TextColumn::make('sucursal.nombre')
                        ->label('')
                        ->icon('heroicon-o-building-storefront')
                        ->placeholder('Sucursal no definida')
                        ->color('gray'),

                    // 🧾 Ticket
                    TextColumn::make('tikete')
                        ->label('')
                        ->formatStateUsing(fn ($state) => $state ? "Ticket: {$state}" : null)
                        ->color('gray'),

                    // 📅 Fecha
                    TextColumn::make('fecha')
                        ->label('')
                        ->date('d M Y')
                        ->icon('heroicon-o-calendar')
                        ->color('gray'),

                    // 👤 Asignador (opcional)
                    TextColumn::make('asignador.name')
                        ->label('')
                        ->formatStateUsing(fn ($state) => $state ? "Asignado por: {$state}" : null)
                        ->color('gray'),

                ])
                ->space(3)
                ->extraAttributes([
                    'class' => 'rounded-xl bg-white shadow-sm p-4',
                ]),
            ])
            ->paginated([10, 20])
            ->defaultSort('fecha', 'desc')
            ->recordActions([])      // ❌ sin acciones
            ->toolbarActions([]);    // ❌ sin bulk actions
    }
}
