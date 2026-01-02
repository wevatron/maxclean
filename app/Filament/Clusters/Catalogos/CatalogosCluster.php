<?php

namespace App\Filament\Clusters\Catalogos;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Enums\SubNavigationPosition;

class CatalogosCluster extends Cluster
{

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static ?string $navigationLabel = 'Catálogos';

}
