<?php

namespace App\Filament\Admin\Resources\Cuentas\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CuentaPagosRelationManager extends RelationManager
{
    protected static string $relationship = 'pagos';

    protected static ?string $title = 'Pagos consolidados de cuenta';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 2))
                    ->color('success')
                    ->sortable(),

                TextColumn::make('metodo_pago')
                    ->label('Método')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => ucfirst((string) $state)),

                TextColumn::make('referencia')
                    ->label('Referencia')
                    ->placeholder('—')
                    ->searchable(),

                IconColumn::make('cancelado')
                    ->label('Cancelado')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}