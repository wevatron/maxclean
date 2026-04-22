<?php

namespace App\Filament\Admin\Resources\Clientes\RelationManagers;

use App\Models\Sucursal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PuntosRelationManager extends RelationManager
{
    protected static string $relationship = 'puntos';

    protected static ?string $title = 'Puntos del Cliente';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->can('Clientes:Gestionar') ?? false;
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('puntos')
                ->label('Cantidad de puntos')
                ->numeric()
                ->minValue(1)
                ->required(),

            TextInput::make('tikete')
                ->label('Número/Folio de tikete')
                ->required(),

            Select::make('sucursal_id')
                ->label('Sucursal')
                ->options(Sucursal::query()->pluck('nombre', 'id')->toArray())
                ->searchable()
                ->required(),

            Hidden::make('fecha')
                ->default(now()),

            Hidden::make('asignado_por')
                ->default(fn () => Auth::id()),
        ]);
    }

    public function table(Table $table): Table
    {
        $puedeGestionar = auth()->user()?->can('Clientes:Gestionar') ?? false;

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('puntos')
                    ->label('Puntos')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tikete')
                    ->label('Tikete/Folio')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('asignador.name')
                    ->label('Asignado por')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar puntos')
                    ->visible($puedeGestionar)
                    ->authorize($puedeGestionar)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['asignado_por'] = Auth::id();
                        $data['fecha'] = now();

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->visible($puedeGestionar)
                    ->authorize($puedeGestionar),

                DeleteAction::make()
                    ->visible($puedeGestionar)
                    ->authorize($puedeGestionar),
            ]);
    }
}