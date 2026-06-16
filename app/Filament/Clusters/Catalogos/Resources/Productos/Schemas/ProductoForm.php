<?php

namespace App\Filament\Clusters\Catalogos\Resources\Productos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del producto')
                    ->description('Configura los productos con inventario, precio de compra y precio de venta para autoservicio.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('sucursal_id')
                                    ->label('Sucursal')
                                    ->relationship('sucursal', 'nombre')
                                    ->default(function () {
                                        $user = auth()->user();

                                        if (! $user) {
                                            return null;
                                        }

                                        $sucursales = $user->sucursales;

                                        return $sucursales->count() === 1 ? $sucursales->first()?->id : null;
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('nombre')
                                    ->label('Nombre')
                                    ->placeholder('Ej. Suavizante')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('precio_base')
                                    ->label('Precio base')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01),

                                TextInput::make('precio_compra')
                                    ->label('Precio de compra')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01),

                                TextInput::make('existencia')
                                    ->label('Existencia')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->default(0),

                                Toggle::make('activo')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Si está desactivado, no aparecerá en autoservicio.'),
                            ]),

                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Ej. Presentación de 1 litro.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
