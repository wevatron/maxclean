<?php

namespace App\Filament\Cliente\Resources\Puntos\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PuntoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('sucursal_id')
                    ->numeric(),
                TextInput::make('asignado_por')
                    ->required()
                    ->numeric(),
                TextInput::make('tikete'),
                TextInput::make('puntos')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('fecha'),
            ]);
    }
}
