<?php

namespace App\Filament\Cliente\Resources\Tickets;

use App\Filament\Cliente\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Cliente\Resources\Tickets\Pages\EditTicket;
use App\Filament\Cliente\Resources\Tickets\Pages\ListTickets;
use App\Filament\Cliente\Resources\Tickets\Schemas\TicketForm;
use App\Filament\Cliente\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $recordTitleAttribute = 'Ticket';
    protected static ?string $pluralLabel = 'Mis Tickets';

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



    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('cliente_id', auth()->id());
    }
    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'view'  => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
