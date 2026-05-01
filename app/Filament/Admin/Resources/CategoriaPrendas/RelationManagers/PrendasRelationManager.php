<?php

namespace App\Filament\Admin\Resources\CategoriaPrendas\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PrendasRelationManager extends RelationManager
{
    protected static string $relationship = 'prendas';

    protected static ?string $title = 'Prendas';

    /* =========================
       FORM
    ========================== */

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Grid::make(2)
                    ->schema([

                        TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

Select::make('categoria_prenda_id')
    ->relationship('categoria', 'nombre')
    ->label('Categoría')
    ->default(fn () => $this->ownerRecord->id)
    ->disabled()
    ->dehydrated()
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

                    ])->columnSpanFull(),
                    ]);
    }

    /* =========================
       TABLE
    ========================== */

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('UID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('ir')
                    ->label('Ver detalle')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn($record) => url('/admin/catalogos/prendas/' . $record->id))
                    ->openUrlInNewTab(), 
            ]);
    }
}
