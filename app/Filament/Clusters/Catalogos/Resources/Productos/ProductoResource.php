<?php

namespace App\Filament\Clusters\Catalogos\Resources\Productos;

use App\Filament\Clusters\Catalogos\Resources\Productos\Pages\CreateProducto;
use App\Filament\Clusters\Catalogos\Resources\Productos\Pages\EditProducto;
use App\Filament\Clusters\Catalogos\Resources\Productos\Pages\ListProductos;
use App\Filament\Clusters\Catalogos\Resources\Productos\Schemas\ProductoForm;
use App\Filament\Clusters\Catalogos\Resources\Productos\Tables\ProductosTable;
use App\Filament\Clusters\ProductosServicios\ProductosServiciosCluster;
use App\Models\Producto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $cluster = ProductosServiciosCluster::class;

    protected static ?string $modelLabel = 'Producto';

    protected static ?string $pluralLabel = 'Productos';

    protected static ?string $navigationLabel = 'Productos';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return ProductoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductos::route('/'),
            'create' => CreateProducto::route('/create'),
            'edit' => EditProducto::route('/{record}/edit'),
        ];
    }
}
