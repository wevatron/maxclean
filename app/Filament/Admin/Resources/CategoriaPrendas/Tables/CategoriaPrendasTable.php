<?php

namespace App\Filament\Admin\Resources\CategoriaPrendas\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class CategoriaPrendasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('nombre')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('prendas_count')
                    ->label('Prendas')
                    ->counts('prendas')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([

                // 🔹 Solo categorías con prendas
                Filter::make('con_prendas')
                    ->label('Con prendas')
                    ->query(fn (Builder $query) => 
                        $query->has('prendas')
                    ),

                // 🔹 Solo categorías vacías
                Filter::make('sin_prendas')
                    ->label('Sin prendas')
                    ->query(fn (Builder $query) => 
                        $query->doesntHave('prendas')
                    ),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
