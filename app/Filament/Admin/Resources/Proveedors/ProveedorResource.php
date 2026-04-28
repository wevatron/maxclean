<?php

namespace App\Filament\Admin\Resources\Proveedors;

use App\Filament\Admin\Resources\Proveedors\Pages\CreateProveedor;
use App\Filament\Admin\Resources\Proveedors\Pages\EditProveedor;
use App\Filament\Admin\Resources\Proveedors\Pages\ListProveedors;
use App\Filament\Admin\Resources\Proveedors\Schemas\ProveedorForm;
use App\Filament\Admin\Resources\Proveedors\Tables\ProveedorsTable;
use App\Models\Proveedor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    protected static ?string $recordTitleAttribute = 'Proveedor';

    public static function form(Schema $schema): Schema
    {
        return ProveedorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProveedorsTable::configure($table);
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
            'index' => ListProveedors::route('/'),
            'create' => CreateProveedor::route('/create'),
            'edit' => EditProveedor::route('/{record}/edit'),
        ];
    }
}
