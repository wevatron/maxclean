<x-filament-panels::page class="!p-0 !max-w-full">
    @if (!$accesoValido)
        <div
            style="
                display:flex;
                align-items:center;
                justify-content:center;
                height:80vh;
                font-size:22px;
                font-weight:600;
                color:#ef4444;
                text-align:center;
            ">
            {{ $mensajeAcceso }}
        </div>
    @else
        <div style="display:flex; height:80vh; width:100%; background:#2b2b2b;">

            <!-- IZQUIERDA -->
            <div style="width:70%; padding:40px; overflow:auto; box-sizing:border-box;">

                <input type="text" wire:model.live="search" placeholder="Buscar prenda..."
                    style="
                        width:100%;
                        padding:16px;
                        border-radius:14px;
                        border:1px solid #555;
                        font-size:18px;
                        margin-bottom:30px;
                        background:#3a3a3a;
                        color:white;
                    " />

                @if ($clienteSeleccionadoId)
                    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:20px;">
                        @foreach ($prendas as $prenda)
                            @php
                                $precioRelacion = $prenda->precios->first();
                                $precioMostrar = $precioRelacion?->precio_normal ?? 0;
                            @endphp

                            <div wire:click="agregarPrenda({{ $prenda->id }})"
                                style="
                                        padding:20px;
                                        border-radius:18px;
                                        background:#3a3a3a;
                                        cursor:pointer;
                                        border:1px solid #4a4a4a;
                                        color:white;
                                        transition:.2s ease;
                                    ">
                                <div style="font-weight:600; font-size:18px;">
                                    {{ $prenda->nombre }}
                                </div>

                                <div style="margin-top:10px; font-size:20px; font-weight:700; color:#0ea5e9;">
                                    ${{ number_format($precioMostrar, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div
                        style="
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                min-height:420px;
                                border-radius:18px;
                                border:1px dashed #4a4a4a;
                                background:#313131;
                                color:#9ca3af;
                                font-size:22px;
                                font-weight:600;
                                text-align:center;
                                padding:30px;
                            ">
                        Selecciona un cliente para mostrar las prendas
                    </div>
                @endif
            </div>

            <!-- DERECHA -->
            <div
                style="
                    width:30%;
                    background:#111827;
                    color:white;
                    padding:40px;
                    display:flex;
                    flex-direction:column;
                    position:relative;
                ">

                <div
                    style="
                            margin-bottom:24px;
                            padding:16px;
                            border-radius:16px;
                            background:#0f172a;
                            border:1px solid #1f2937;
                        ">
                    <div
                        style="
                                display:flex;
                                align-items:center;
                                justify-content:space-between;
                                gap:12px;
                                margin-bottom:{{ $clientePanelAbierto ? '16px' : '0' }};
                            ">
                        <div>
                            <div style="font-size:20px; font-weight:700;">Cliente</div>

                            @if ($clienteSeleccionadoId)
                                <div style="font-size:13px; color:#9ca3af; margin-top:4px;">
                                    {{ $clienteSeleccionadoNombre }}
                                </div>
                            @else
                                <div style="font-size:13px; color:#9ca3af; margin-top:4px;">
                                    Sin cliente seleccionado
                                </div>
                            @endif
                        </div>

                        <button type="button" wire:click="toggleClientePanel"
                            style="
                                    width:38px;
                                    height:38px;
                                    border:none;
                                    border-radius:10px;
                                    background:#2563eb;
                                    color:white;
                                    cursor:pointer;
                                    font-size:18px;
                                    font-weight:700;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    flex-shrink:0;
                                "
                            title="Mostrar / ocultar cliente">
                            @if ($clientePanelAbierto)
                                −
                            @else
                                ✎
                            @endif
                        </button>
                    </div>

                    @if ($clientePanelAbierto)
                        <div style="margin-bottom:18px; position:relative;">
                            <input type="text" wire:model.live.debounce.300ms="clienteSearch"
                                placeholder="Buscar por nombre, teléfono o correo..."
                                style="
                                        width:100%;
                                        padding:14px;
                                        border-radius:12px;
                                        border:1px solid #374151;
                                        background:#1f2937;
                                        color:white;
                                        font-size:16px;
                                    " />

                            @if (!empty($clientesEncontrados))
                                <div
                                    style="
                                            position:absolute;
                                            top:100%;
                                            left:0;
                                            right:0;
                                            margin-top:8px;
                                            background:#111827;
                                            border:1px solid #374151;
                                            border-radius:12px;
                                            overflow:hidden;
                                            z-index:50;
                                            box-shadow:0 10px 25px rgba(0,0,0,.35);
                                            max-height:260px;
                                            overflow-y:auto;
                                        ">
                                    @foreach ($clientesEncontrados as $cliente)
                                        <button type="button" wire:click="seleccionarCliente({{ $cliente['id'] }})"
                                            style="
                                                    width:100%;
                                                    text-align:left;
                                                    padding:12px 14px;
                                                    border:none;
                                                    background:transparent;
                                                    color:white;
                                                    cursor:pointer;
                                                    border-bottom:1px solid #1f2937;
                                                ">
                                            <div style="font-weight:600;">
                                                {{ $cliente['name'] }}
                                            </div>

                                            @if (!empty($cliente['whatsapp']))
                                                <div style="font-size:13px; color:#9ca3af;">
                                                    Whatsapp: ({{ $cliente['whatsapp'] }})
                                                </div>
                                            @endif

                                            {{--                                             @if (!empty($cliente['email']))
                                                <div style="font-size:12px; color:#6b7280;">
                                                    {{ $cliente['email'] }}
                                                </div>
                                            @endif --}}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if ($clienteSeleccionadoId)
                            <div
                                style="
                                        margin-bottom:16px;
                                        padding:14px;
                                        border-radius:12px;
                                        background:#1f2937;
                                        display:flex;
                                        justify-content:space-between;
                                        align-items:center;
                                        gap:12px;
                                    ">
                                <div>
                                    <div style="font-size:13px; color:#9ca3af;">Cliente seleccionado</div>
                                    <div style="font-weight:700;">{{ $clienteSeleccionadoNombre }}</div>
                                </div>

                                <button type="button" wire:click="limpiarCliente"
                                    style="
                                        background:#ef4444;
                                        color:white;
                                        border:none;
                                        padding:8px 12px;
                                        border-radius:10px;
                                        cursor:pointer;
                                    ">
                                    Quitar
                                </button>
                            </div>
                        @endif

                        <a href="{{ $this->getCrearClienteUrl() }}" target="_blank"
                            style="
                                    display:inline-block;
                                    width:100%;
                                    margin-bottom:10px;
                                    padding:12px 16px;
                                    border-radius:12px;
                                    background:#2563eb;
                                    color:white;
                                    text-decoration:none;
                                    font-weight:700;
                                    text-align:center;
                                    box-sizing:border-box;
                                ">
                            Registrar cliente en otra pestaña
                        </a>
                    @endif
                </div>

                <div style="margin: 10px 0 24px 0;">
                    <label
                        style="
                                display:flex;
                                align-items:center;
                                justify-content:space-between;
                                gap:12px;
                                background:#0f172a;
                                border:1px solid #1f2937;
                                border-radius:14px;
                                padding:14px 16px;
                                cursor:pointer;
                            ">
                        <div>
                            <div style="font-size:16px; font-weight:700; color:white;">
                                Modo express
                            </div>
                        </div>

                        <input type="checkbox" wire:model.live="modoExpress"
                            style="
                                width:20px;
                                height:20px;
                                accent-color:#2563eb;
                                cursor:pointer;
                            ">
                    </label>
                </div>
{{-- 
                <h2 style="font-size:22px; margin-bottom:20px;">Ticket</h2>
 --}}
                <div style="flex:1; overflow:auto;">
                    @forelse ($items as $index => $item)
                        <div
                            style="
                                display:flex;
                                justify-content:space-between;
                                align-items:center;
                                padding:14px;
                                margin-bottom:12px;
                                background:#1f2937;
                                border-radius:14px;
                            ">
                            <div>
                                <div style="font-weight:600;">
                                    {{ $item['nombre'] }}
                                </div>
                                <div style="font-size:14px; color:#9ca3af;">
                                    x{{ $item['cantidad'] }}
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:15px;">
                                <span>
                                    ${{ number_format($this->calcularSubtotalItem($item), 2) }}
                                </span>

                                <button type="button" wire:click="eliminarItem({{ $index }})"
                                    style="
                                        background:none;
                                        border:none;
                                        color:#ef4444;
                                        font-size:22px;
                                        cursor:pointer;
                                    ">
                                    ×
                                </button>
                            </div>
                        </div>
                    @empty
                        <div
                            style="
                                padding:20px;
                                border-radius:14px;
                                background:#1f2937;
                                color:#9ca3af;
                                text-align:center;
                            ">
                            Aún no has agregado prendas al ticket.
                        </div>
                    @endforelse
                </div>

                <div style="border-top:1px solid #374151; padding-top:30px; margin-top:20px;">
                    <div style="font-size:42px; font-weight:800; color:#22c55e;">
                        ${{ number_format($total, 2) }}
                    </div>

                    @if ($clienteSeleccionadoId)
                        <button type="button" wire:click="abrirModalCobro"
                            style="
                                    width:100%;
                                    padding:18px;
                                    border-radius:18px;
                                    background:#22c55e;
                                    border:none;
                                    font-size:20px;
                                    font-weight:700;
                                    margin-top:10px;
                                    color:white;
                                    cursor:pointer;
                                ">
                            COBRAR
                        </button>
                    @else
                        <div
                            style="
                                    margin-top:20px;
                                    padding:14px;
                                    border-radius:14px;
                                    background:#1f2937;
                                    color:#9ca3af;
                                    text-align:center;
                                    font-size:14px;
                                ">
                            Selecciona un cliente para continuar
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if ($modalCobroAbierto)
        <div
            style="
                position:fixed;
                inset:0;
                background:rgba(0,0,0,.6);
                display:flex;
                align-items:center;
                justify-content:center;
                z-index:9999;
            ">
            <div
                style="
                    width:100%;
                    max-width:520px;
                    background:white;
                    border-radius:18px;
                    padding:24px;
                    box-shadow:0 25px 50px rgba(0,0,0,.35);
                ">
                <div style="font-size:22px; font-weight:700; margin-bottom:18px; color:#111827;">
                    Registrar pago
                </div>

                <div style="font-size:18px; margin-bottom:15px; color:#111827;">
                    Total: <strong>${{ number_format($total, 2) }}</strong>
                </div>

                <div style="display:flex; gap:10px; margin-bottom:15px; flex-wrap:wrap;">
                    <button type="button" wire:click="montoCero"
                        style="
                            padding:10px 14px;
                            border:none;
                            border-radius:10px;
                            background:#6b7280;
                            color:white;
                            cursor:pointer;
                        ">
                        0%
                    </button>

                    <button type="button" wire:click="montoMitad"
                        style="
                            padding:10px 14px;
                            border:none;
                            border-radius:10px;
                            background:#f59e0b;
                            color:white;
                            cursor:pointer;
                        ">
                        50%
                    </button>

                    <button type="button" wire:click="montoTotal"
                        style="
                            padding:10px 14px;
                            border:none;
                            border-radius:10px;
                            background:#22c55e;
                            color:white;
                            cursor:pointer;
                        ">
                        100%
                    </button>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                        Monto a pagar / anticipo
                    </label>

                    <input type="number" step="0.01" min="0" wire:model.live="montoTemporal"
                        style="
                            width:100%;
                            padding:12px;
                            border-radius:10px;
                            border:1px solid #d1d5db;
                            color:#111827;
                        " />
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                        Método de pago
                    </label>

                    <select wire:model="metodoPago"
                        style="
                            width:100%;
                            padding:12px;
                            border-radius:10px;
                            border:1px solid #d1d5db;
                            color:#111827;
                        ">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                    </select>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" wire:click="cerrarModalCobro"
                        style="
                            padding:12px 16px;
                            border:none;
                            border-radius:10px;
                            background:#6b7280;
                            color:white;
                            cursor:pointer;
                        ">
                        Cancelar
                    </button>

                    <button type="button" wire:click="confirmarCobro"
                        style="
                            padding:12px 16px;
                            border:none;
                            border-radius:10px;
                            background:#22c55e;
                            color:white;
                            cursor:pointer;
                        ">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
