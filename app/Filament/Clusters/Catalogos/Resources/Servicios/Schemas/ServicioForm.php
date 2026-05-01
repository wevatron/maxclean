<?php

namespace App\Filament\Clusters\Catalogos\Resources\Servicios\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServicioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del producto o servicio')
                    ->description('Configura los servicios y productos que estarán disponibles en la venta general o en autoservicio.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre')
                                    ->placeholder('Ej. Ciclo de lavado')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('precio_base')
                                    ->label('Precio base')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01),
                            ]),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Ej. Renta de lavadora por un ciclo.')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('activo')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Si está desactivado, no aparecerá en autoservicio.'),
                    ])->columnSpanFull(),
            ]);
    }
}