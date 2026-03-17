<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class AdminDashboard extends Page
{
    protected string $view = 'filament.admin.pages.admin-dashboard';
    public ?int $sucursalId = null;
    public ?string $camino = null;

    /* =========================
        NIVEL 1
    ========================= */
    public function seleccionarSucursal(int $id): void
    {
        $this->sucursalId = $id;
        $this->camino = null;
    }

    /* =========================
        NIVEL 2
    ========================= */
    public function seleccionarCamino(string $camino): void
    {
        $this->camino = $camino;
    }

    /* =========================
        NAVEGACIÓN
    ========================= */
    public function volverACaminos(): void
    {
        $this->camino = null;
    }

    public function volverASucursales(): void
    {
        $this->sucursalId = null;
        $this->camino = null;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }
}
