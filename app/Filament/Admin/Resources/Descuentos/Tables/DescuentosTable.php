<?php

namespace App\Filament\Admin\Resources\Descuentos\Tables;

use App\Filament\Admin\Resources\Descuentos\DescuentoResource;
use App\Models\Descuento;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DescuentosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('inicio')
                    ->label('Inicio')
                    ->date()
                    ->sortable(),

                TextColumn::make('fin')
                    ->label('Fin')
                    ->date()
                    ->sortable(),

                TextColumn::make('porcentaje')
                    ->label('Porcentaje')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 2) . '%' : '-')
                    ->sortable(),

                TextColumn::make('fijo')
                    ->label('Monto fijo')
                    ->formatStateUsing(fn ($state) => $state !== null ? '$' . number_format((float) $state, 2) : '-')
                    ->sortable(),

                TextColumn::make('nivel')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => ucfirst((string) $state))
                    ->sortable(),

                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('nivel')
                    ->label('Nivel')
                    ->options([
                        'personal' => 'Personal',
                        'global' => 'Global',
                    ]),

                TernaryFilter::make('activo')
                    ->label('Estado')
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn (Descuento $record): string => DescuentoResource::getUrl('view', [
                'record' => $record,
            ]));
    }
}
