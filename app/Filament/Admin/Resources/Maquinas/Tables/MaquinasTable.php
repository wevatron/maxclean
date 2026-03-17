<?php

namespace App\Filament\Admin\Resources\Maquinas\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Sucursal;
use App\Models\TipoMaquina;

class MaquinasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tipo.nombre')
                    ->label('Tipo de máquina')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'libre',
                        'warning' => 'ocupada',
                        'danger'  => 'fuera_de_servicio',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'libre' => 'Libre',
                        'ocupada' => 'Ocupada',
                        'fuera_de_servicio' => 'Fuera de servicio',
                    }),
            ])
            ->filters([
                // 🔹 Filtro por estado
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'libre' => 'Libre',
                        'ocupada' => 'Ocupada',
                        'fuera_de_servicio' => 'Fuera de servicio',
                    ]),

                // 🔹 Filtro por sucursal
                SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->options(
                        Sucursal::query()
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray()
                    )
                    ->searchable(),

                // 🔹 Filtro por tipo de máquina
                SelectFilter::make('tipo_maquina_id')
                    ->label('Tipo de máquina')
                    ->options(
                        TipoMaquina::query()
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray()
                    )
                    ->searchable(),
            ]);
    }
}
