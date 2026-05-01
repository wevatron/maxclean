<?php

namespace App\Filament\Cliente\Resources\Tickets\Pages;

use App\Filament\Cliente\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos')
                ->badge(fn () => $this->contarTodos()),

            'encargo' => Tab::make('Encargo')
                ->badge(fn () => $this->contarPorTipo('encargo'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $query->where('tipo', 'encargo')
                ),

            'express' => Tab::make('Express')
                ->badge(fn () => $this->contarPorTipo('encargo_express'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $query->where('tipo', 'encargo_express')
                ),

            'kilo' => Tab::make('Por kilo')
                ->badge(fn () => $this->contarPorTipo('encargo_kilo'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $query->where('tipo', 'encargo_kilo')
                ),

            'autoservicio' => Tab::make('Autoservicio')
                ->badge(fn () => $this->contarPorTipo('autoservicio'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $query->where('tipo', 'autoservicio')
                ),
        ];
    }

    private function contarTodos(): int
    {
        return static::getResource()::getEloquentQuery()
            ->count();
    }

    private function contarPorTipo(string $tipo): int
    {
        return static::getResource()::getEloquentQuery()
            ->where('tipo', $tipo)
            ->count();
    }
}