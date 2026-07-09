<?php

namespace App\Filament\Admin\Resources\Tickets\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTabs(): array
    {
        return [

            'detallado' => Tab::make('Detallado')
                ->badge(fn () => $this->contarProcesoPendiente('detallado'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $this->sinCuentasCanceladas($query)
                        ->whereHas(
                            'procesos',
                            fn ($q) =>
                            $q->where('proceso', 'detallado')
                                ->where('completado', false)
                        )
                ),

            'lavado' => Tab::make('Lavado')
                ->badge(fn () => $this->contarProcesoPendiente('lavado'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $this->sinCuentasCanceladas($query)
                        ->whereHas(
                            'procesos',
                            fn ($q) =>
                            $q->where('proceso', 'lavado')
                                ->where('completado', false)
                        )
                ),

            'secado' => Tab::make('Secado')
                ->badge(fn () => $this->contarProcesoPendiente('secado'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $this->sinCuentasCanceladas($query)
                        ->whereHas(
                            'procesos',
                            fn ($q) =>
                            $q->where('proceso', 'secado')
                                ->where('completado', false)
                        )
                ),

            'doblado y empaquetado' => Tab::make('Doblado y Empaquetado')
                ->badge(fn () => $this->contarProcesoPendiente('doblado y empaquetado'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $this->sinCuentasCanceladas($query)
                        ->whereHas(
                            'procesos',
                            fn ($q) =>
                            $q->where('proceso', 'doblado y empaquetado')
                                ->where('completado', false)
                        )
                ),

            'por entregar' => Tab::make('Por entregar')
                ->badge(fn () => $this->contarProcesoPendiente('entregado'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $this->sinCuentasCanceladas($query)
                        ->whereHas('procesos', function ($q) {
                            $q->where('proceso', 'entregado')
                                ->where('completado', false);
                        })
                ),

            'entregado' => Tab::make('Entregado')
                ->badge(fn () => $this->contarEntregadosHoy())
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $this->sinCuentasCanceladas($query)
                        ->whereHas('procesos', function ($q) {
                            $q->where('proceso', 'entregado')
                                ->where('completado', true)
                                ->whereDate('updated_at', Carbon::today()->toDateString());
                        })
                ),

            'autoservicio' => Tab::make('Autoservicio')
                ->badge(fn () => $this->contarAutoservicio())
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $this->sinCuentasCanceladas($query)
                        ->where('tipo', 'autoservicio')
                        ->where(
                            fn (Builder $query) => $this->excluirPagadasEnCorte($query)
                        )
                ),

            /* 'todos' => Tab::make('Todos'), */
        ];
    }

    private function sinCuentasCanceladas(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNull('cuenta_id')
                ->orWhereHas('cuenta', function (Builder $cuentaQuery) {
                    $cuentaQuery->where('estatus', '!=', 'cancelada');
                });
        });
    }

    private function contarProcesoPendiente(string $proceso): int
    {
        return $this->sinCuentasCanceladas(static::getResource()::getEloquentQuery())
            ->whereHas('procesos', function ($q) use ($proceso) {
                $q->where('proceso', $proceso)
                    ->where('completado', false);
            })
            ->count();
    }

    private function contarEntregadosHoy(): int
    {
        return $this->sinCuentasCanceladas(static::getResource()::getEloquentQuery())
            ->whereHas('procesos', function ($q) {
                $q->where('proceso', 'entregado')
                    ->where('completado', true)
                    ->whereDate('updated_at', Carbon::today()->toDateString());
            })
            ->count();
    }

    private function contarAutoservicio(): int
    {
        return $this->sinCuentasCanceladas(static::getResource()::getEloquentQuery())
            ->where('tipo', 'autoservicio')
            ->where(fn (Builder $query) => $this->excluirPagadasEnCorte($query))
            ->count();
    }

    private function excluirPagadasEnCorte(Builder $query): Builder
    {
        $tablaTickets = (new Ticket())->getTable();

        return $query->whereRaw(
            "NOT (
                (
                    SELECT COALESCE(SUM(tp.monto), 0)
                    FROM ticket_pagos tp
                    WHERE tp.ticket_id = {$tablaTickets}.id
                      AND tp.cancelado = 0
                ) >= {$tablaTickets}.total
                AND NOT EXISTS (
                    SELECT 1
                    FROM ticket_pagos tp2
                    WHERE tp2.ticket_id = {$tablaTickets}.id
                      AND tp2.cancelado = 0
                      AND tp2.corte_id IS NULL
                )
            )"
        );
    }
}
