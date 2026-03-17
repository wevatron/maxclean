<?php

namespace App\Filament\Admin\Resources\CategoriaPrendas\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;

class CategoriaPrendaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Grid::make(2)
                    ->schema([

                        TextInput::make('nombre')
                            ->label('Nombre de la categoría')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Ej. Camisas, Edredones, Cortinas...')
                            ->columnSpanFull(),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Describe qué tipo de prendas incluye esta categoría...')
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}
