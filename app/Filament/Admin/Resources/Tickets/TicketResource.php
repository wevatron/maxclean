<?php

namespace App\Filament\Admin\Resources\Tickets;

use App\Filament\Admin\Resources\Tickets\Pages\ViewTicket;
use App\Filament\Admin\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Admin\Resources\Tickets\Pages\EditTicket;
use App\Filament\Admin\Resources\Tickets\Pages\ListTickets;
use App\Filament\Admin\Resources\Tickets\Schemas\TicketForm;
use App\Filament\Admin\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $recordTitleAttribute = 'Ticket';

    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
            ->visiblePara(Auth::user());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'edit' => EditTicket::route('/{record}/edit'),
            'view' => ViewTicket::route('/{record}'),
        ];
    }
}
