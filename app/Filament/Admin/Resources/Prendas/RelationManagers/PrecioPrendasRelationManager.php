<?php

namespace App\Filament\Admin\Resources\Prendas\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PrecioPrendasRelationManager extends RelationManager
{
    protected static string $relationship = 'precios'; 
    // 👆 Asegúrate que en Prenda tengas:
    // public function precios() { return $this->hasMany(PrecioPrenda::class); }

    protected static ?string $title = 'Precios por sucursal';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([

                        Forms\Components\Select::make('sucursal_id')
                            ->relationship('sucursal', 'nombre')
                            ->required(),

                        Forms\Components\TextInput::make('precio_normal')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        Forms\Components\TextInput::make('precio_express')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('precio_paquete')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('piezas_por_paquete')
                            ->numeric(),

                        Forms\Components\Textarea::make('observaciones')
                            ->columnSpanFull(),

                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal'),

                Tables\Columns\TextColumn::make('precio_normal')
                    ->money('MXN'),

                Tables\Columns\TextColumn::make('precio_express')
                    ->money('MXN'),

                Tables\Columns\TextColumn::make('precio_paquete')
                    ->money('MXN'),

                Tables\Columns\TextColumn::make('piezas_por_paquete'),

            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
