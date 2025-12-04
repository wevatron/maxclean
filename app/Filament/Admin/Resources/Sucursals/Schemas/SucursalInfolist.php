<?php

namespace App\Filament\Admin\Resources\Sucursals\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SucursalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('direccion')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('telefono')
                    ->placeholder('-'),
                TextEntry::make('whatsapp'),
                TextEntry::make('latitud')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('longitud')
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
