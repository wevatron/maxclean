<?php

namespace App\Filament\Admin\Resources\Tickets\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
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
            
        ];
    }

    public function getTabs(): array
    {
        return [

            'detallado' => Tab::make('Detallado')
                ->badge(fn () => $this->contarProcesoPendiente('detallado'))
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $query->whereHas(
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
                    $query->whereHas(
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
                    $query->whereHas(
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
                    $query->whereHas(
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
                    $query->whereHas('procesos', function ($q) {
                        $q->where('proceso', 'entregado')
                            ->where('completado', false);
                    })
                ),

            'entregado' => Tab::make('Entregado')
                ->badge(fn () => $this->contarEntregadosRecientes())
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $query->whereHas('procesos', function ($q) {
                        $q->where('proceso', 'entregado')
                            ->where('completado', true)
                            ->where('updated_at', '>=', Carbon::now()->subHours(48));
                    })
                ),

            'autoservicio' => Tab::make('Autoservicio')
                ->badge(fn () => $this->contarAutoservicio())
                ->modifyQueryUsing(
                    fn (Builder $query) =>
                    $query->where('tipo', 'autoservicio')
                ),

            /* 'todos' => Tab::make('Todos'), */
        ];
    }

    private function contarProcesoPendiente(string $proceso): int
    {
        return static::getResource()::getEloquentQuery()
            ->whereHas('procesos', function ($q) use ($proceso) {
                $q->where('proceso', $proceso)
                    ->where('completado', false);
            })
            ->count();
    }

    private function contarEntregadosRecientes(): int
    {
        return static::getResource()::getEloquentQuery()
            ->whereHas('procesos', function ($q) {
                $q->where('proceso', 'entregado')
                    ->where('completado', true)
                    ->where('updated_at', '>=', Carbon::now()->subHours(48));
            })
            ->count();
    }

    private function contarAutoservicio(): int
    {
        return static::getResource()::getEloquentQuery()
            ->where('tipo', 'autoservicio')
            ->count();
    }
}