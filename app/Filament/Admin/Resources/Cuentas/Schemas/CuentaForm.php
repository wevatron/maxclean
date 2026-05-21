<?php

namespace App\Filament\Admin\Resources\Cuentas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CuentaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la cuenta')
                    ->columns(3)
                    ->schema([
                        TextInput::make('numero')
                            ->label('Número')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('sucursal_id')
                            ->label('Sucursal')
                            ->relationship('sucursal', 'nombre')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('total')
                            ->label('Total')
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('total_pagado')
                            ->label('Pagado')
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('saldo')
                            ->label('Saldo')
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('estatus')
                            ->label('Estatus')
                            ->options([
                                'abierta' => 'Abierta',
                                'parcial' => 'Parcial',
                                'pagada' => 'Pagada',
                                'cancelada' => 'Cancelada',
                            ])
                            ->disabled()
                            ->dehydrated(false),

                        DateTimePicker::make('abierta_en')
                            ->label('Abierta en')
                            ->disabled()
                            ->dehydrated(false),

                        DateTimePicker::make('cerrada_en')
                            ->label('Cerrada en')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columnSpanFull(),

                /* Section::make('Notas internas')
                    ->schema([
                        Textarea::make('notas')
                            ->label('Notas')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]), */
            ]);
    }
}