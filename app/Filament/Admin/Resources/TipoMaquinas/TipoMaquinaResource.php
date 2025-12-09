<?php

namespace App\Filament\Admin\Resources\TipoMaquinas;

use App\Filament\Admin\Resources\TipoMaquinas\Pages\CreateTipoMaquina;
use App\Filament\Admin\Resources\TipoMaquinas\Pages\EditTipoMaquina;
use App\Filament\Admin\Resources\TipoMaquinas\Pages\ListTipoMaquinas;
use App\Filament\Admin\Resources\TipoMaquinas\Pages\ViewTipoMaquina;
use App\Filament\Admin\Resources\TipoMaquinas\Schemas\TipoMaquinaForm;
use App\Filament\Admin\Resources\TipoMaquinas\Schemas\TipoMaquinaInfolist;
use App\Filament\Admin\Resources\TipoMaquinas\Tables\TipoMaquinasTable;
use App\Models\TipoMaquina;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TipoMaquinaResource extends Resource
{
    protected static ?string $model = TipoMaquina::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    protected static ?string $recordTitleAttribute = 'Tipo de maquinas';

    public static function form(Schema $schema): Schema
    {
        return TipoMaquinaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TipoMaquinaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoMaquinasTable::configure($table);
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
            'index' => ListTipoMaquinas::route('/'),
            'create' => CreateTipoMaquina::route('/create'),
            'view' => ViewTipoMaquina::route('/{record}'),
            'edit' => EditTipoMaquina::route('/{record}/edit'),
        ];
    }
}
