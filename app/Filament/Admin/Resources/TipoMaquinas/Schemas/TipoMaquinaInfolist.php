<?php

namespace App\Filament\Admin\Resources\TipoMaquinas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TipoMaquinaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('descripcion')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('capacidad_kg')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tiempo_minimo')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tiempo_maximo')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
