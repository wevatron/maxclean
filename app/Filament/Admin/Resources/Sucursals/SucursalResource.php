<?php

namespace App\Filament\Admin\Resources\Sucursals;

use App\Filament\Admin\Resources\Sucursals\Pages\CreateSucursal;
use App\Filament\Admin\Resources\Sucursals\Pages\EditSucursal;
use App\Filament\Admin\Resources\Sucursals\Pages\ListSucursals;
use App\Filament\Admin\Resources\Sucursals\Pages\ViewSucursal;
use App\Filament\Admin\Resources\Sucursals\Schemas\SucursalForm;
use App\Filament\Admin\Resources\Sucursals\Schemas\SucursalInfolist;
use App\Filament\Admin\Resources\Sucursals\Tables\SucursalsTable;
use App\Filament\Clusters\Catalogos\CatalogosCluster;
use App\Models\Sucursal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SucursalResource extends Resource
{
    protected static ?string $model = Sucursal::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;
    protected static ?string $cluster = CatalogosCluster::class;
    protected static ?string $recordTitleAttribute = 'Sucursales';
    protected static ?string $navigationLabel = 'Sucursales';
    protected static ?string $pluralLabel = 'Sucursales';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SucursalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SucursalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SucursalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSucursals::route('/'),
            'create' => CreateSucursal::route('/create'),
            'view' => ViewSucursal::route('/{record}'),
            'edit' => EditSucursal::route('/{record}/edit'),
        ];
    }
}
