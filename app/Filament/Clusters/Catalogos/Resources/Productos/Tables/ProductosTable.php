<?php

namespace App\Filament\Clusters\Catalogos\Resources\Productos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Sucursal;
use Filament\Tables\Table;

class ProductosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Producto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('precio_base')
                    ->label('Precio')
                    ->money('MXN')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('existencia')
                    ->label('Existencia')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => (int) $state > 0 ? 'success' : 'danger'),

                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->options(
                        Sucursal::query()
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray()
                    )
                    ->searchable(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                EditAction::make()->label('Editar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
