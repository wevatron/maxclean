<x-filament::page>

@php
    $sucursal = $sucursalId
        ? \App\Models\Sucursal::find($sucursalId)
        : null;

    $nombreCamino = match ($camino) {
        'servicio' => 'Servicio por encargo',
        'equipos'  => 'Equipos disponibles',
        default    => null,
    };
@endphp

{{-- =====================================================
HEADER · MIGA DE PAN CLARA
===================================================== --}}
<div style="margin-bottom:1rem;">
    <div style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;color:#6b7280;flex-wrap:wrap;">

        {{-- Sucursales --}}
        <span
            style="cursor:pointer;color:#2563eb;"
            wire:click="volverASucursales"
        >
            Sucursales
        </span>

        {{-- Sucursal actual --}}
        @if ($sucursal)
            <span>›</span>
            <span
                style="cursor:pointer;color:#2563eb;"
                wire:click="volverACaminos"
            >
                {{ $sucursal->nombre }}
            </span>
        @endif

        {{-- Opción --}}
        @if ($nombreCamino)
            <span>›</span>
            <span style="font-weight:600;color:#2563eb;">
                {{ $nombreCamino }}
            </span>
        @endif

    </div>
</div>


{{-- =====================================================
NIVEL 1 · SUCURSALES
===================================================== --}}
@if (is_null($sucursalId))

<x-filament::section heading="Selecciona una sucursal">

    <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:1.5rem;width:100%;">
        @foreach (\App\Models\Sucursal::all() as $s)
            <x-filament::card
                style="width:260px;cursor:pointer;"
                wire:click="seleccionarSucursal({{ $s->id }})"
            >
                <div style="display:flex;flex-direction:column;align-items:center;gap:.75rem;padding:2rem;">
                    <x-heroicon-o-building-storefront
                        style="width:40px;height:40px;color:#3b82f6;"
                    />
                    <strong>{{ $s->nombre }}</strong>
                </div>
            </x-filament::card>
        @endforeach
    </div>

</x-filament::section>
@endif


{{-- =====================================================
NIVEL 2 · CAMINOS
===================================================== --}}
@if (!is_null($sucursalId) && is_null($camino))

<x-filament::section heading="Selecciona una opción">

    <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:1.5rem;width:100%;">

        <x-filament::card
            style="width:260px;cursor:pointer;"
            wire:click="seleccionarCamino('servicio')"
        >
            <div style="display:flex;flex-direction:column;align-items:center;gap:.75rem;padding:2rem;">
                <x-heroicon-o-clipboard-document-list
                    style="width:40px;height:40px;color:#22c55e;"
                />
                <strong>Servicio por encargo</strong>
            </div>
        </x-filament::card>

        <x-filament::card
            style="width:260px;cursor:pointer;"
            wire:click="seleccionarCamino('equipos')"
        >
            <div style="display:flex;flex-direction:column;align-items:center;gap:.75rem;padding:2rem;">
                <x-heroicon-o-cog-6-tooth
                    style="width:40px;height:40px;color:#f59e0b;"
                />
                <strong>Equipos disponibles</strong>
            </div>
        </x-filament::card>

    </div>

</x-filament::section>
@endif


{{-- =====================================================
NIVEL 3 · EQUIPOS DISPONIBLES
===================================================== --}}
@if (!is_null($sucursalId) && $camino === 'equipos')

@php
    $maquinas = \App\Models\Maquina::with('tipo')
        ->where('sucursal_id', $sucursalId)
        ->get();
@endphp

<x-filament::section heading="Equipos disponibles">

    @if ($maquinas->isEmpty())
        <p style="text-align:center;color:#9ca3af;">
            No hay máquinas registradas en esta sucursal.
        </p>
    @else

        <div
            style="
                display:flex;
                flex-wrap:wrap;
                justify-content:center;
                gap:1.5rem;
                width:100%;
            "
        >

            @foreach ($maquinas as $maquina)

@php
    switch ($maquina->status) {
        case 'libre':
            $icon = 'heroicon-o-check-circle';
            $color = '#22c55e'; // verde
            $label = 'Disponible';
            break;
            
        case 'ocupada':
            $icon = 'heroicon-o-lock-closed';
            $color = '#f59e0b'; // amarillo
            $label = 'Ocupada';
            break;

        case 'fuera_de_servicio':
            $icon = 'heroicon-o-x-circle';
            $color = '#ef4444'; // rojo
            $label = 'Fuera de servicio';
            break;

        default:
            $icon = 'heroicon-o-question-mark-circle';
            $color = '#9ca3af'; // gris
            $label = 'Desconocido';
    }
@endphp


                <x-filament::card style="width:260px;">

                    <div
                        style="
                            display:flex;
                            flex-direction:column;
                            align-items:center;
                            gap:.5rem;
                            padding:1.75rem 1rem;
                            text-align:center;
                        "
                    >
                        {{-- Icono --}}
                        <x-dynamic-component
                            :component="$icon"
                            style="width:42px;height:42px;color:{{ $color }};"
                        />

                        {{-- Tipo de máquina --}}
                        <strong>
                            {{ $maquina->tipo->nombre ?? 'Máquina' }}
                        </strong>

                        {{-- Estado --}}
                        <span style="font-size:.85rem;color:{{ $color }};">
                            {{ $label }}
                        </span>

                    </div>

                </x-filament::card>

            @endforeach

        </div>

    @endif

</x-filament::section>
@endif

@if (auth()->user()?->hasRole('super_admin'))
    <x-filament::section heading="Mantenimiento temporal" style="margin-top:2rem;">
        <div
            style="
                display:flex;
                flex-direction:column;
                gap:12px;
                padding:18px;
                border-radius:16px;
                background:#fff7ed;
                border:1px solid #fed7aa;
            ">
            <div style="color:#9a3412;font-weight:700;font-size:16px;">
                Zona de pruebas
            </div>
            <div style="color:#9a3412;font-size:14px;line-height:1.5;">
                Este botón vacía cuentas, tickets, pagos y cierres de caja. No borra puntos, usuarios ni catálogos.
            </div>

            <button
                type="button"
                wire:click="vaciarDatosPruebas"
                wire:confirm="Esto eliminará cuentas, tickets, pagos y cierres de caja. Los puntos se conservarán. ¿Continuar?"
                style="
                    align-self:flex-start;
                    padding:12px 16px;
                    border:none;
                    border-radius:12px;
                    background:#dc2626;
                    color:white;
                    font-weight:700;
                    cursor:pointer;
                ">
                Vaciar datos de pruebas
            </button>
        </div>
    </x-filament::section>
@endif


</x-filament::page>
