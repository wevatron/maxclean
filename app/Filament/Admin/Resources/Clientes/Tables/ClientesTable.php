<?php

namespace App\Filament\Admin\Resources\Clientes\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Models\User;

class ClientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('whatsapp')
                    ->searchable()
                    ->label('Teléfono/Whatsapp')
                    ->visibleFrom('md'),

                TextColumn::make('contacto_mobile')
                    ->label('Contacto')
                    ->state(function (User $record): string {
                        return trim((string) ($record->email ?: 'Sin correo'));
                    })
                    ->description(function (User $record): string {
                        return trim((string) ($record->whatsapp ?: 'Sin teléfono'));
                    })
                    ->wrap()
                    ->hiddenFrom('md'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
