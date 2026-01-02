<?php

namespace App\Filament\Admin\Resources\Clientes;

use App\Filament\Admin\Resources\ClienteResource\RelationManagers\PuntosRelationManager as RelationManagersPuntosRelationManager;
use App\Filament\Admin\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Admin\Resources\Clientes\Pages\EditCliente;
use App\Filament\Admin\Resources\Clientes\Pages\ListClientes;
use App\Filament\Admin\Resources\Clientes\Pages\ViewCliente;
use App\Filament\Admin\Resources\Clientes\RelationManagers\PuntosRelationManager;
use App\Filament\Admin\Resources\Clientes\Schemas\ClienteForm;
use App\Filament\Admin\Resources\Clientes\Schemas\ClienteInfolist;
use App\Filament\Admin\Resources\Clientes\Tables\ClientesTable;
use App\Filament\Clusters\Catalogos\CatalogosCluster;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClienteResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Clientes';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $pluralLabel = 'Clientes';

    protected static ?string $cluster = CatalogosCluster::class;
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ClienteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClienteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagersPuntosRelationManager::class,
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', fn ($q) => $q->where('name', 'cliente'));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientes::route('/'),
            'create' => CreateCliente::route('/create'),
            'view' => ViewCliente::route('/{record}'),
            'edit' => EditCliente::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
