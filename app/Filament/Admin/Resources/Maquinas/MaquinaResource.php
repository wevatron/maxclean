<?php

namespace App\Filament\Admin\Resources\Maquinas;

use App\Filament\Admin\Resources\Maquinas\Pages\CreateMaquina;
use App\Filament\Admin\Resources\Maquinas\Pages\EditMaquina;
use App\Filament\Admin\Resources\Maquinas\Pages\ListMaquinas;
use App\Filament\Admin\Resources\Maquinas\Pages\ViewMaquina;
use App\Filament\Admin\Resources\Maquinas\Schemas\MaquinaForm;
use App\Filament\Admin\Resources\Maquinas\Schemas\MaquinaInfolist;
use App\Filament\Admin\Resources\Maquinas\Tables\MaquinasTable;
use App\Models\Maquina;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaquinaResource extends Resource
{
    protected static ?string $model = Maquina::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $recordTitleAttribute = 'Maquinas';

    public static function form(Schema $schema): Schema
    {
        return MaquinaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MaquinaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaquinasTable::configure($table);
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
            'index' => ListMaquinas::route('/'),
            'create' => CreateMaquina::route('/create'),
            'view' => ViewMaquina::route('/{record}'),
            'edit' => EditMaquina::route('/{record}/edit'),
        ];
    }
}
