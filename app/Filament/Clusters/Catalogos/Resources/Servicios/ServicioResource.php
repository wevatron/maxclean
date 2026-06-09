<?php

namespace App\Filament\Clusters\Catalogos\Resources\Servicios;

use App\Filament\Clusters\Catalogos\Resources\Servicios\Pages\CreateServicio;
use App\Filament\Clusters\Catalogos\Resources\Servicios\Pages\EditServicio;
use App\Filament\Clusters\Catalogos\Resources\Servicios\Pages\ListServicios;
use App\Filament\Clusters\Catalogos\Resources\Servicios\Schemas\ServicioForm;
use App\Filament\Clusters\Catalogos\Resources\Servicios\Tables\ServiciosTable;
use App\Filament\Clusters\ProductosServicios\ProductosServiciosCluster;
use App\Models\Servicio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $cluster = ProductosServiciosCluster::class;

    protected static ?string $modelLabel = 'Servicio';

    protected static ?string $pluralLabel = 'Servicios';

    protected static ?string $navigationLabel = 'Servicios';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return ServicioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiciosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServicios::route('/'),
            'create' => CreateServicio::route('/create'),
            'edit' => EditServicio::route('/{record}/edit'),
        ];
    }
}
