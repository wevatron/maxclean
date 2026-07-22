<?php

namespace App\Filament\Admin\Resources\FusionClientes\Pages;

use App\Filament\Admin\Resources\FusionClientes\FusionClientesResource;
use App\Models\Cuenta;
use App\Models\CuentaPago;
use App\Models\Punto;
use App\Models\Ticket;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class FusionClientes extends Page
{
    protected static string $resource = FusionClientesResource::class;

    protected string $view = 'filament.admin.resources.fusion-clientes.pages.fusion-clientes';

    public string $buscarPrincipal = '';

    public string $buscarSecundario = '';

    public array $resultadosPrincipal = [];

    public array $resultadosSecundario = [];

    public ?int $clientePrincipalId = null;

    public ?int $clienteSecundarioId = null;

    public ?string $clientePrincipalNombre = null;

    public ?string $clienteSecundarioNombre = null;

    public bool $confirmarFusion = false;

    public bool $procesando = false;

    public function mount(): void
    {
        $this->resultadosPrincipal = [];
        $this->resultadosSecundario = [];
    }

    public function updatedBuscarPrincipal(): void
    {
        $this->resultadosPrincipal = $this->buscarClientes($this->buscarPrincipal, $this->clienteSecundarioId);
    }

    public function updatedBuscarSecundario(): void
    {
        $this->resultadosSecundario = $this->buscarClientes($this->buscarSecundario, $this->clientePrincipalId);
    }

    public function seleccionarPrincipal(int $id): void
    {
        $cliente = $this->buscarClientePorId($id);

        if (! $cliente) {
            return;
        }

        if ($this->clienteSecundarioId === $cliente->id) {
            Notification::make()
                ->title('Elige dos clientes distintos')
                ->warning()
                ->send();

            return;
        }

        $this->clientePrincipalId = $cliente->id;
        $this->clientePrincipalNombre = $cliente->name;
        $this->buscarPrincipal = $cliente->name;
        $this->resultadosPrincipal = [];
    }

    public function seleccionarSecundario(int $id): void
    {
        $cliente = $this->buscarClientePorId($id);

        if (! $cliente) {
            return;
        }

        if ($this->clientePrincipalId === $cliente->id) {
            Notification::make()
                ->title('Elige dos clientes distintos')
                ->warning()
                ->send();

            return;
        }

        $this->clienteSecundarioId = $cliente->id;
        $this->clienteSecundarioNombre = $cliente->name;
        $this->buscarSecundario = $cliente->name;
        $this->resultadosSecundario = [];
    }

    public function limpiarPrincipal(): void
    {
        $this->clientePrincipalId = null;
        $this->clientePrincipalNombre = null;
        $this->buscarPrincipal = '';
        $this->resultadosPrincipal = [];
        $this->confirmarFusion = false;
    }

    public function limpiarSecundario(): void
    {
        $this->clienteSecundarioId = null;
        $this->clienteSecundarioNombre = null;
        $this->buscarSecundario = '';
        $this->resultadosSecundario = [];
        $this->confirmarFusion = false;
    }

    public function fusionarClientes(): void
    {
        if ($this->procesando) {
            return;
        }

        if (! $this->clientePrincipalId || ! $this->clienteSecundarioId) {
            Notification::make()
                ->title('Selecciona ambos clientes')
                ->danger()
                ->send();

            return;
        }

        if ($this->clientePrincipalId === $this->clienteSecundarioId) {
            Notification::make()
                ->title('El cliente principal y el secundario no pueden ser el mismo')
                ->danger()
                ->send();

            return;
        }

        if (! $this->confirmarFusion) {
            Notification::make()
                ->title('Confirma la fusión antes de continuar')
                ->warning()
                ->send();

            return;
        }

        $this->procesando = true;

        try {
            DB::transaction(function () {
                $principal = User::query()
                    ->withTrashed()
                    ->whereHas('roles', fn ($query) => $query->where('name', 'cliente'))
                    ->whereKey($this->clientePrincipalId)
                    ->lockForUpdate()
                    ->first();

                $secundario = User::query()
                    ->withTrashed()
                    ->whereHas('roles', fn ($query) => $query->where('name', 'cliente'))
                    ->whereKey($this->clienteSecundarioId)
                    ->lockForUpdate()
                    ->first();

                if (! $principal || ! $secundario) {
                    throw new \RuntimeException('No se pudieron localizar ambos clientes.');
                }

                if ($principal->id === $secundario->id) {
                    throw new \RuntimeException('No puedes fusionar un cliente consigo mismo.');
                }

                if ($principal->trashed()) {
                    $principal->restore();
                }

                Ticket::query()
                    ->where('cliente_id', $secundario->id)
                    ->update(['cliente_id' => $principal->id]);

                Cuenta::query()
                    ->where('cliente_id', $secundario->id)
                    ->update(['cliente_id' => $principal->id]);

                CuentaPago::query()
                    ->where('cliente_id', $secundario->id)
                    ->update(['cliente_id' => $principal->id]);

                Punto::query()
                    ->where('user_id', $secundario->id)
                    ->update(['user_id' => $principal->id]);

                $secundario->delete();
            });

            $principal = $this->buscarClientePorId($this->clientePrincipalId);
            $secundario = $this->buscarClientePorId($this->clienteSecundarioId);

            $this->limpiarPrincipal();
            $this->limpiarSecundario();

            Notification::make()
                ->title('Clientes fusionados correctamente')
                ->body(
                    'Se consolidó el histórico del cliente duplicado en el cliente principal. ' .
                    'Tickets, cuentas, pagos y puntos quedaron unificados.'
                )
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('No se pudo fusionar')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->procesando = false;
        }
    }

    public function buscarClientes(string $texto, ?int $excluirId = null): array
    {
        $texto = trim($texto);

        if ($texto === '') {
            return [];
        }

        return User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'cliente'))
            ->where(function ($query) use ($texto) {
                $query->where('name', 'like', "%{$texto}%")
                    ->orWhere('email', 'like', "%{$texto}%")
                    ->orWhere('whatsapp', 'like', "%{$texto}%");
            })
            ->when($excluirId, fn ($query, $id) => $query->where('id', '!=', $id))
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(fn (User $cliente) => [
                'id' => $cliente->id,
                'name' => $cliente->name,
                'email' => $cliente->email,
                'whatsapp' => $cliente->whatsapp,
                'trashed' => $cliente->trashed(),
            ])
            ->all();
    }

    public function buscarClientePorId(int $id): ?User
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'cliente'))
            ->find($id);
    }

    public function resumenCliente(?int $id): array
    {
        if (! $id) {
            return [
                'tickets' => 0,
                'cuentas' => 0,
                'pagos' => 0,
                'puntos' => 0,
            ];
        }

        return [
            'tickets' => Ticket::query()->where('cliente_id', $id)->count(),
            'cuentas' => Cuenta::query()->where('cliente_id', $id)->count(),
            'pagos' => CuentaPago::query()->where('cliente_id', $id)->count(),
            'puntos' => (int) Punto::query()->where('user_id', $id)->sum('puntos'),
        ];
    }

    public function getHeaderActions(): array
    {
        return [];
    }
}
