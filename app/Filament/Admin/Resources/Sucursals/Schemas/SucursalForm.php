<?php

namespace App\Filament\Admin\Resources\Sucursals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SucursalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Textarea::make('direccion')
                    ->columnSpanFull(),
                TextInput::make('telefono')
                    ->tel(),
                TextInput::make('whatsapp')
                    ->required(),
                TextInput::make('latitud')
                    ->numeric(),
                TextInput::make('longitud')
                    ->numeric(),
            ]);
    }
}
