<?php

namespace App\Filament\Admin\Resources\HistorialTickets;

use App\Filament\Admin\Resources\HistorialTickets\Pages\ListHistorialTickets;
use App\Filament\Admin\Resources\HistorialTickets\Tables\HistorialTicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HistorialTicketsResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    protected static ?string $navigationLabel = 'Historial de tickets';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'numero';

    public static function table(Table $table): Table
    {
        return HistorialTicketsTable::configure($table);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('Tickets:Gestionar') || auth()->user()?->hasRole('super_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('Tickets:Gestionar') || auth()->user()?->hasRole('super_admin');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->visiblePara(Auth::user())
            ->with([
                'cliente',
                'status',
                'cuenta',
                'sucursal',
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHistorialTickets::route('/'),
        ];
    }
}
