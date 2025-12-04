<?php

namespace App\Filament\Admin\Resources\Maquinas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MaquinaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('tipo_maquina_id')
                    ->label('Tipo de máquina')
                    ->relationship('tipo', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('status')
                    ->options(['libre' => 'Libre', 'ocupada' => 'Ocupada', 'fuera_de_servicio' => 'Fuera de servicio'])
                    ->default('libre')
                    ->required(),
            ]);
    }
}
