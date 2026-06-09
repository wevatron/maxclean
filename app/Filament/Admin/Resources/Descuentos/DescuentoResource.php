<?php

namespace App\Filament\Admin\Resources\Descuentos;

use App\Filament\Admin\Resources\Descuentos\Pages\CreateDescuento;
use App\Filament\Admin\Resources\Descuentos\Pages\EditDescuento;
use App\Filament\Admin\Resources\Descuentos\Pages\ListDescuentos;
use App\Filament\Admin\Resources\Descuentos\Pages\ViewDescuento;
use App\Filament\Admin\Resources\Descuentos\Schemas\DescuentoForm;
use App\Filament\Admin\Resources\Descuentos\Schemas\DescuentoInfolist;
use App\Filament\Admin\Resources\Descuentos\Tables\DescuentosTable;
use App\Filament\Clusters\Catalogos\CatalogosCluster;
use App\Models\Descuento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DescuentoResource extends Resource
{
    protected static ?string $model = Descuento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static ?string $cluster = CatalogosCluster::class;

    protected static ?string $navigationLabel = 'Descuentos';
    protected static ?int $navigationSort = 6;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('ViewAny:Descuento') || auth()->user()?->hasRole('super_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('ViewAny:Descuento') || auth()->user()?->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return DescuentoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DescuentoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DescuentosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDescuentos::route('/'),
            'create' => CreateDescuento::route('/create'),
            'view' => ViewDescuento::route('/{record}'),
            'edit' => EditDescuento::route('/{record}/edit'),
        ];
    }
}
