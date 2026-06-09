<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\Cuentas\CuentaResource;
use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Models\Cuenta;
use App\Models\Ticket;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Escaner extends Page
{
    protected string $view = 'filament.admin.pages.escaner';

    protected static string|UnitEnum|null $navigationGroup = 'Gestión';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $navigationLabel = 'F9 Escáner';

    protected static ?int $navigationSort = 9;

    public ?string $ultimoCodigo = null;

    public ?string $mensaje = 'Apunta la cámara al QR para abrir la cuenta o el ticket.';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(['empleado', 'super_admin']);
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    public function resolverCodigo(string $codigo)
    {
        $codigo = trim($codigo);
        $this->ultimoCodigo = $codigo;

        if ($codigo === '') {
            $this->mensaje = 'El QR no contiene un valor válido.';

            return null;
        }

        if (ctype_digit($codigo)) {
            $cuenta = Cuenta::query()->find((int) $codigo);

            if ($cuenta) {
                $this->mensaje = 'Redirigiendo a la cuenta #' . $cuenta->numero;

                return redirect()->to(CuentaResource::getUrl('edit', [
                    'record' => $cuenta,
                ]));
            }

            $ticket = Ticket::query()->with('cuenta')->find((int) $codigo);

            if ($ticket?->cuenta) {
                $this->mensaje = 'Redirigiendo a la cuenta del ticket #' . $ticket->numero;

                return redirect()->to(CuentaResource::getUrl('edit', [
                    'record' => $ticket->cuenta,
                ]));
            }

            if ($ticket) {
                $this->mensaje = 'Redirigiendo al ticket #' . $ticket->numero;

                return redirect()->to(TicketResource::getUrl('view', [
                    'record' => $ticket,
                ]));
            }
        }

        $this->mensaje = 'No encontramos una cuenta o ticket para este código.';

        return null;
    }
}
