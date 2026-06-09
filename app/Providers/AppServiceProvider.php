<?php

namespace App\Providers;

use App\Filament\Admin\Pages\Autoservicio;
use App\Filament\Admin\Pages\CorteCaja;
use App\Filament\Admin\Pages\Dashboard;
use App\Filament\Admin\Pages\Escaner;
use App\Filament\Admin\Pages\PorEncargo;
use App\Filament\Admin\Pages\PorKilo;
use App\Filament\Admin\Resources\Clientes\ClienteResource;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use Filament\View\PanelsRenderHook;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('es');

        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_END,
            function (): string {
                if (! auth()->check() || ! auth()->user()?->hasRole('empleado')) {
                    return '';
                }

                $links = [
                    'F1' => Dashboard::getUrl(),
                    'F2' => PorEncargo::getUrl(),
                    'F3' => PorKilo::getUrl(),
                    'F4' => Autoservicio::getUrl(),
                    'F5' => \App\Filament\Admin\Pages\CorteCaja::getUrl(),
                    'F6' => ClienteResource::getUrl(),
                    'F7' => \App\Filament\Admin\Resources\Cuentas\CuentaResource::getUrl(),
                    'F8' => \App\Filament\Admin\Resources\Tickets\TicketResource::getUrl(),
                    'F9' => Escaner::getUrl(),
                ];

                $payload = json_encode($links, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                return <<<HTML
<script>
(function () {
    const shortcuts = {$payload};

    window.addEventListener('keydown', function (event) {
        if (event.defaultPrevented) return;
        if (event.ctrlKey || event.metaKey || event.altKey) return;
        if (event.target && ['INPUT', 'TEXTAREA', 'SELECT'].includes(event.target.tagName)) return;

        const key = event.key;
        if (!shortcuts[key]) return;

        event.preventDefault();
        window.location.href = shortcuts[key];
    });
})();
</script>
HTML;
            }
        );
    }
}
