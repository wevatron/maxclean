<?php

namespace App\Filament\Admin\Resources\Prendas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrendaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Select::make('categoria_prenda_id')
                    ->relationship('categoria', 'nombre')
                    ->label('Categoría')
                    ->required(),

                Select::make('tamano')
                    ->label('Tamaño')
                    ->options([
                        'chico'   => 'Chico',
                        'mediano' => 'Mediano',
                        'grande'  => 'Grande',
                        'delgado' => 'Delgado',
                        'normal'  => 'Normal',
                        'jumbo'   => 'Jumbo',
                        'especial'=> 'Especial',
                    ])
                    ->searchable()
                    ->nullable(),

                Select::make('unidad')
                    ->label('Unidad')
                    ->options([
                        'pieza'   => 'Pieza',
                        'kg'      => 'Kilogramo (kg)',
                        'paquete' => 'Paquete',
                        'par'     => 'Par',
                    ])
                    ->default('pieza')
                    ->required()
                    ->searchable(),

                TextInput::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(1000),
            ]);
    }
}
