<?php

namespace App\Filament\Admin\Resources\Descuentos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DescuentoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('inicio')
                    ->date()
                    ->label('Inicio'),

                TextEntry::make('fin')
                    ->date()
                    ->label('Fin'),

                TextEntry::make('porcentaje')
                    ->label('Porcentaje')
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 2) . '%' : '-'),

                TextEntry::make('fijo')
                    ->label('Monto fijo')
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state !== null ? '$' . number_format((float) $state, 2) : '-'),

                TextEntry::make('nivel')
                    ->badge()
                    ->label('Nivel')
                    ->formatStateUsing(fn (?string $state) => ucfirst((string) $state)),

                TextEntry::make('activo')
                    ->badge()
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => $state ? 'Activo' : 'Inactivo'),

                TextEntry::make('created_at')
                    ->dateTime()
                    ->label('Creado en')
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->dateTime()
                    ->label('Actualizado en')
                    ->placeholder('-'),
            ]);
    }
}
