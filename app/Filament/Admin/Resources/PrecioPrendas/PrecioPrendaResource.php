<?php

namespace App\Filament\Admin\Resources\PrecioPrendas;

use App\Filament\Admin\Resources\PrecioPrendas\Pages\CreatePrecioPrenda;
use App\Filament\Admin\Resources\PrecioPrendas\Pages\EditPrecioPrenda;
use App\Filament\Admin\Resources\PrecioPrendas\Pages\ListPrecioPrendas;
use App\Filament\Admin\Resources\PrecioPrendas\Pages\ViewPrecioPrenda;
use App\Filament\Admin\Resources\PrecioPrendas\Schemas\PrecioPrendaForm;
use App\Filament\Admin\Resources\PrecioPrendas\Schemas\PrecioPrendaInfolist;
use App\Filament\Admin\Resources\PrecioPrendas\Tables\PrecioPrendasTable;
use App\Filament\Clusters\Catalogos\CatalogosCluster;
use App\Models\PrecioPrenda;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrecioPrendaResource extends Resource
{
    protected static ?string $model = PrecioPrenda::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bookmark;
    protected static ?string $cluster = CatalogosCluster::class;
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'PrecioPrenda';

    public static function form(Schema $schema): Schema
    {
        return PrecioPrendaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrecioPrendaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrecioPrendasTable::configure($table);
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
            'index' => ListPrecioPrendas::route('/'),
            'create' => CreatePrecioPrenda::route('/create'),
            'view' => ViewPrecioPrenda::route('/{record}'),
            'edit' => EditPrecioPrenda::route('/{record}/edit'),
        ];
    }
}
