<?php

namespace App\Filament\Admin\Resources\Prendas\Schemas;

use Dom\Text;
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
                Select::make('categoria_id')
                    ->relationship('categoria', 'nombre')
                    ->label('Categoría')
                    ->required(),
                TextInput::make('tamano')
                    ->label('Tamaño')
                    ->required()
                    ->maxLength(100),
                TextInput::make('unidad')
                    ->label('Unidad')
                    ->required()
                    ->maxLength(100),
                TextInput::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(1000),  
            ]);
    }
}
