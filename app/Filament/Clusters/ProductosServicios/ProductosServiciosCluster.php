<?php

namespace App\Filament\Clusters\ProductosServicios;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ProductosServiciosCluster extends Cluster
{
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static string|UnitEnum|null $navigationGroup = 'Gestión';
    protected static ?int $navigationSort = 0;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = 'Productos y servicios';
}
