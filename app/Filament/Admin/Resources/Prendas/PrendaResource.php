<?php

namespace App\Filament\Admin\Resources\Prendas;

use App\Filament\Admin\Resources\Prendas\Pages\CreatePrenda;
use App\Filament\Admin\Resources\Prendas\Pages\EditPrenda;
use App\Filament\Admin\Resources\Prendas\Pages\ListPrendas;
use App\Filament\Admin\Resources\Prendas\Pages\ViewPrenda;
use App\Filament\Admin\Resources\Prendas\Schemas\PrendaForm;
use App\Filament\Admin\Resources\Prendas\Schemas\PrendaInfolist;
use App\Filament\Admin\Resources\Prendas\Tables\PrendasTable;
use App\Filament\Clusters\Catalogos\CatalogosCluster;
use App\Models\Prenda;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Clusters\Settings\SettingsCluster;

class PrendaResource extends Resource
{
    protected static ?string $model = Prenda::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Camera;
    protected static ?string $recordTitleAttribute = 'Prenda';
    protected static ?int $navigationSort = 3;
    protected static ?string $cluster = CatalogosCluster::class;

    public static function form(Schema $schema): Schema
    {
        return PrendaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrendaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrendasTable::configure($table);
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
            'index' => ListPrendas::route('/'),
            'create' => CreatePrenda::route('/create'),
            'view' => ViewPrenda::route('/{record}'),
            'edit' => EditPrenda::route('/{record}/edit'),
        ];
    }
}
