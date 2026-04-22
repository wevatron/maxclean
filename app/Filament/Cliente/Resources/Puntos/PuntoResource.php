<?php

namespace App\Filament\Cliente\Resources\Puntos;

use App\Filament\Cliente\Resources\Puntos\Pages\CreatePunto;
use App\Filament\Cliente\Resources\Puntos\Pages\EditPunto;
use App\Filament\Cliente\Resources\Puntos\Pages\ListPuntos;
use App\Filament\Cliente\Resources\Puntos\Schemas\PuntoForm;
use App\Filament\Cliente\Resources\Puntos\Tables\PuntosTable;
use App\Models\Punto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class PuntoResource extends Resource
{
    protected static ?string $model = Punto::class;


    protected static ?string $recordTitleAttribute = 'Punto';

    // 📌 TÍTULO EN SIDEBAR Y PÁGINA
    protected static ?string $navigationLabel = 'Mis puntos';

    // 📌 ÍCONO (elige el que más te guste)
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;
    // alternativas buenas:
    // heroicon-o-sparkles
    // heroicon-o-gift
    // heroicon-o-trophy

    // 📌 ORDEN EN MENÚ
    protected static ?int $navigationSort = 1;

    // 📌 TÍTULO GRANDE DE LA PÁGINA
    public static function getTitle(): string
    {
        return 'Mis puntos';
    }
    public static function getPluralLabel(): string
    {
        $total = Punto::where('user_id', auth()->id())
            ->where('puntos', '>', 0)
            ->sum('puntos');

        return "Mis puntos ({$total})";
    }

    // 📌 SUBTÍTULO / DESCRIPCIÓN
    public static function getSubtitle(): ?string
    {
        return 'Historial de puntos que has obtenido en tus visitas';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('cliente');
    }
    // ❌ No CRUD
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return PuntoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PuntosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->where('puntos', '>', 0);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPuntos::route('/'),
            'create' => CreatePunto::route('/create'),
            'edit' => EditPunto::route('/{record}/edit'),
        ];
    }
}
