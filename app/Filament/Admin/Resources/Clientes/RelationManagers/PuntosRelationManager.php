<?php

namespace App\Filament\Admin\Resources\ClienteResource\RelationManagers;

use App\Models\Punto;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PuntosRelationManager extends RelationManager
{
    protected static string $relationship = 'puntos';

    protected static ?string $title = 'Puntos del Cliente';
    /**
     * FORM — Filament 4 uses Schema instead of Form
     */
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('puntos')
                ->label('Cantidad de puntos')
                ->numeric()
                ->minValue(1)
                ->required(),

            DateTimePicker::make('fecha')
                ->label('Fecha')
                ->default(now())
                ->required(),

            Hidden::make('asignado_por')
                ->default(Auth::id()),
        ]);
    }

    /**
     * TABLE — still uses Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('puntos')
                    ->label('Puntos'),

                Tables\Columns\TextColumn::make('fecha')
                    ->dateTime('d/m/Y H:i')
                    ->disabled()
                    ->label('Fecha'),

                Tables\Columns\TextColumn::make('asignador.name')
                    ->label('Asignado por'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar puntos')
                    ->mutateFormDataUsing(function ($data) {
                        $data['asignado_por'] = Auth::id();
                        $data['fecha'] = now();
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

   public function isReadOnly(): bool
{
    return false;
}
}