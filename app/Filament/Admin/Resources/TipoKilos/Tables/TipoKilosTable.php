<?php

namespace App\Filament\Admin\Resources\TipoKilos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TipoKilosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('orden')
            ->reorderable('orden')
            ->columns([
                TextColumn::make('clave')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('precio')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('minimo')
                    ->label('Mínimo')
                    ->formatStateUsing(fn ($state) => rtrim(rtrim(number_format((float) $state, 2), '0'), '.') . ' kg')
                    ->sortable(),

                TextColumn::make('orden')
                    ->sortable(),

                IconColumn::make('activo')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
