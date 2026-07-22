<?php

namespace App\Filament\Admin\Resources\TipoKilos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TipoKiloInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('clave'),
                TextEntry::make('nombre'),
                TextEntry::make('descripcion')
                    ->columnSpanFull(),
                TextEntry::make('precio')
                    ->money('MXN'),
                TextEntry::make('minimo')
                    ->formatStateUsing(fn ($state) => rtrim(rtrim(number_format((float) $state, 2), '0'), '.') . ' kg'),
                TextEntry::make('orden'),
                TextEntry::make('activo')
                    ->badge(),
            ]);
    }
}
