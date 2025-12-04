<?php

namespace App\Filament\Admin\Resources\Maquinas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                        'danger'  => 'ocupada',
                        'gray'    => 'fuera_de_servicio',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'libre' => 'Libre',
                        'ocupada' => 'Ocupada',
                        'fuera_de_servicio' => 'Fuera de servicio',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'libre' => 'Libre',
                        'ocupada' => 'Ocupada',
                        'fuera_de_servicio' => 'Fuera de servicio',
                    ]),
            ]);
    }
}
