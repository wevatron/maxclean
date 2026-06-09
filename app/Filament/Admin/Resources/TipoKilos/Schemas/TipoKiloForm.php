<?php

namespace App\Filament\Admin\Resources\TipoKilos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TipoKiloForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('clave')
                    ->required()
                    ->maxLength(80)
                    ->helperText('Llave interna estable para identificar el tipo de lavado.'),

                TextInput::make('nombre')
                    ->required()
                    ->maxLength(120),

                Textarea::make('descripcion')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('precio')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->minValue(0),

                TextInput::make('orden')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Toggle::make('activo')
                    ->default(true),
            ]);
    }
}
