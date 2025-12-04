<?php

namespace App\Filament\Admin\Resources\TipoMaquinas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TipoMaquinaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
                TextInput::make('capacidad_kg')
                    ->numeric(),
                TextInput::make('tiempo_minimo')
                    ->numeric(),
                TextInput::make('tiempo_maximo')
                    ->numeric(),
            ]);
    }
}
