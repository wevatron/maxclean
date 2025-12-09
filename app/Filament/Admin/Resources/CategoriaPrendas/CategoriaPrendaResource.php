<?php

namespace App\Filament\Admin\Resources\CategoriaPrendas;

use App\Filament\Admin\Resources\CategoriaPrendas\Pages\CreateCategoriaPrenda;
use App\Filament\Admin\Resources\CategoriaPrendas\Pages\EditCategoriaPrenda;
use App\Filament\Admin\Resources\CategoriaPrendas\Pages\ListCategoriaPrendas;
use App\Filament\Admin\Resources\CategoriaPrendas\Pages\ViewCategoriaPrenda;
use App\Filament\Admin\Resources\CategoriaPrendas\Schemas\CategoriaPrendaForm;
use App\Filament\Admin\Resources\CategoriaPrendas\Schemas\CategoriaPrendaInfolist;
use App\Filament\Admin\Resources\CategoriaPrendas\Tables\CategoriaPrendasTable;
use App\Models\CategoriaPrenda;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoriaPrendaResource extends Resource
{
    protected static ?string $model = CategoriaPrenda::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CategoriaPrenda';

    public static function form(Schema $schema): Schema
    {
        return CategoriaPrendaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CategoriaPrendaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriaPrendasTable::configure($table);
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
            'index' => ListCategoriaPrendas::route('/'),
            'create' => CreateCategoriaPrenda::route('/create'),
            'view' => ViewCategoriaPrenda::route('/{record}'),
            'edit' => EditCategoriaPrenda::route('/{record}/edit'),
        ];
    }
}
