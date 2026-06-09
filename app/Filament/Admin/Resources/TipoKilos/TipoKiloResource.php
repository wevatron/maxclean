<?php

namespace App\Filament\Admin\Resources\TipoKilos;

use App\Filament\Admin\Resources\TipoKilos\Pages\CreateTipoKilo;
use App\Filament\Admin\Resources\TipoKilos\Pages\EditTipoKilo;
use App\Filament\Admin\Resources\TipoKilos\Pages\ListTipoKilos;
use App\Filament\Admin\Resources\TipoKilos\Pages\ViewTipoKilo;
use App\Filament\Admin\Resources\TipoKilos\Schemas\TipoKiloForm;
use App\Filament\Admin\Resources\TipoKilos\Schemas\TipoKiloInfolist;
use App\Filament\Admin\Resources\TipoKilos\Tables\TipoKilosTable;
use App\Filament\Clusters\Catalogos\CatalogosCluster;
use App\Models\TipoKilo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TipoKiloResource extends Resource
{
    protected static ?string $model = TipoKilo::class;
    protected static ?string $cluster = CatalogosCluster::class;
    protected static ?string $recordTitleAttribute = 'nombre';
    protected static ?string $navigationLabel = 'Tipos de lavado';
    protected static ?int $navigationSort = 6;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function form(Schema $schema): Schema
    {
        return TipoKiloForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TipoKiloInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoKilosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTipoKilos::route('/'),
            'create' => CreateTipoKilo::route('/create'),
            'view' => ViewTipoKilo::route('/{record}'),
            'edit' => EditTipoKilo::route('/{record}/edit'),
        ];
    }
}
