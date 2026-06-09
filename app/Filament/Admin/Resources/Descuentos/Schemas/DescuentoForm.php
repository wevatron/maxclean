<?php

namespace App\Filament\Admin\Resources\Descuentos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DescuentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del descuento')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('inicio')
                            ->label('Inicio')
                            ->native(false)
                            ->required(),

                        DatePicker::make('fin')
                            ->label('Fin')
                            ->native(false)
                            ->required(),

                        TextInput::make('porcentaje')
                            ->label('Porcentaje')
                            ->numeric()
                            ->suffix('%')
                            ->helperText('Usa este campo si el descuento es porcentual.')
                            ->placeholder('Ej. 15.00'),

                        TextInput::make('fijo')
                            ->label('Monto fijo')
                            ->numeric()
                            ->prefix('$')
                            ->helperText('Usa este campo si el descuento es un monto fijo.')
                            ->placeholder('Ej. 100.00'),

                        Select::make('nivel')
                            ->label('Nivel')
                            ->options([
                                'personal' => 'Personal',
                                'global' => 'Global',
                            ])
                            ->default('global')
                            ->required(),

                        Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
