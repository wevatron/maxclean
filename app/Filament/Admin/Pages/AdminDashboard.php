<?php

namespace App\Filament\Admin\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Page
{
    protected string $view = 'filament.admin.pages.admin-dashboard';
    public ?int $sucursalId = null;
    public ?string $camino = null;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

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

    public function vaciarDatosPruebas(): void
    {
        abort_unless(auth()->user()?->hasRole('super_admin'), 403);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ([
                'ticket_pagos',
                'cuenta_pagos',
                'ticket_productos',
                'ticket_servicios',
                'ticket_items',
                'ticket_procesos',
                'tickets',
                'cuentas',
                'cortes_caja',
            ] as $table) {
                DB::table($table)->truncate();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            Notification::make()
                ->title('Datos de pruebas vaciados')
                ->body('Se limpiaron cuentas, tickets, pagos y cierres de caja. Los puntos se conservaron.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            Notification::make()
                ->title('No se pudo vaciar la información')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }

    public function getHeading(): string
    {
        return '';
    }
    public function getSubheading(): ?string
{
    return null;
}
}
