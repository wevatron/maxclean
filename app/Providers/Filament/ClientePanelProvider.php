<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureUserIsCliente;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ClientePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('cliente')
            ->path('cliente')
            ->colors([
                'primary' => '#00AEEF',   // azul estilo Max&Clean
                'gray'    => '#64748b',
            ])
            ->brandLogo(asset('img/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->brandName(' ') // vacío si no quieres texto
            ->globalSearch(false)
            ->discoverResources(in: app_path('Filament/Cliente/Resources'), for: 'App\Filament\Cliente\Resources')
            ->discoverPages(in: app_path('Filament/Cliente/Pages'), for: 'App\Filament\Cliente\Pages')
          /*   ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Cliente/Widgets'), for: 'App\Filament\Cliente\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ]) */
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->userMenuItems([
                'logout' => Action::make('logout')
                    ->label('Salir')
                    ->action(function () {
                        auth()->logout();

                        return redirect('/');
                    }),
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsCliente::class,
            ]);
    }
}
