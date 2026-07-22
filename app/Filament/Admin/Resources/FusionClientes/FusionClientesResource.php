<?php

namespace App\Filament\Admin\Resources\FusionClientes;

use App\Filament\Admin\Resources\FusionClientes\Pages\FusionClientes;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FusionClientesResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|UnitEnum|null $navigationGroup = 'Gestión';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Fusionar clientes';

    protected static ?string $pluralLabel = 'Fusionar clientes';

    protected static ?int $navigationSort = 6;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('Clientes:Gestionar');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('Clientes:Gestionar');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', fn ($query) => $query->where('name', 'cliente'));
    }

    public static function getPages(): array
    {
        return [
            'index' => FusionClientes::route('/'),
        ];
    }
}
