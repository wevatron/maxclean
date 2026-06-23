<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Override;

class Dashboard extends Page
{
    protected string $view = 'filament.admin.pages.dashboard';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::HomeModern;

    protected static ?string $navigationLabel = 'F1';
    protected static ?int $navigationSort = -100;

    public function getHeading(): string
    {
        return '';
    }
    public function getSubheading(): ?string
    {
        return null;
    }
    #[Override]
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(['empleado']);
    }
}
