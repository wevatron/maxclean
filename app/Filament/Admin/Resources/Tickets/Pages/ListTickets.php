<?php

namespace App\Filament\Admin\Resources\Tickets\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use Filament\Actions\CreateAction;
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
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas(
                        'procesos',
                        fn($q) =>
                        $q->where('proceso', 'detallado')
                            ->where('completado', false)
                    )
                ),

            'lavado' => Tab::make('Lavado')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas(
                        'procesos',
                        fn($q) =>
                        $q->where('proceso', 'lavado')
                            ->where('completado', false)
                    )
                ),

            'secado' => Tab::make('Secado')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas(
                        'procesos',
                        fn($q) =>
                        $q->where('proceso', 'secado')
                            ->where('completado', false)
                    )
                ),

            'doblado y empaquetado' => Tab::make('Doblado y Empaquetado')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas(
                        'procesos',
                        fn($q) =>
                        $q->where('proceso', 'doblado y empaquetado')
                            ->where('completado', false)
                    )
                ),

            'por entregar' => Tab::make('Por entregar')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas('procesos', function ($q) {

                        $q->where('proceso', 'entregado')
                            ->where('completado', false);
                    })
                ),

            'entregado' => Tab::make('Entregado')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas('procesos', function ($q) {

                        $q->where('proceso', 'entregado')
                            ->where('completado', true)
                            ->where('updated_at', '>=', Carbon::now()->subHours(48));
                    })
                ),

           /*  'todos' => Tab::make('Todos'), */
        ];
    }
}
