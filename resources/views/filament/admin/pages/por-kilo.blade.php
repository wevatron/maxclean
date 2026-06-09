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
        <div style="position:relative; display:flex; height:80vh; width:100%; background:#2b2b2b;">
            <div
                style="
                    position:absolute;
                    bottom:6px;
                    left:16px;
                    z-index:20;
                    display:flex;
                    align-items:center;
                    gap:10px;
                    padding:12px 16px;
                    border-radius:999px;
                    background:rgba(15, 23, 42, 0.92);
                    border:1px solid rgba(148, 163, 184, 0.18);
                    box-shadow:0 12px 30px rgba(0,0,0,.22);
                    backdrop-filter:blur(10px);
                    color:white;
                    pointer-events:none;
                ">
                <div
                    style="
                        width:34px;
                        height:34px;
                        border-radius:999px;
                        display:grid;
                        place-items:center;
                        background:linear-gradient(135deg, #3b82f6, #60a5fa);
                        box-shadow:inset 0 1px 0 rgba(255,255,255,.2);
                    ">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" style="width:18px; height:18px;">
                        <path d="M12 3.5v17" stroke="white" stroke-width="1.75" stroke-linecap="round"/>
                        <path d="M6 8.5h12" stroke="white" stroke-width="1.75" stroke-linecap="round"/>
                        <path d="M7.5 8.5 4.5 13a3 3 0 0 0 6 0l-3-4.5Zm9 0-3 4.5a3 3 0 0 0 6 0l-3-4.5Z" stroke="white" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="M9 8.5 12 5l3 3.5" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div style="display:flex; flex-direction:column; line-height:1;">
                    <span style="font-size:12px; text-transform:uppercase; letter-spacing:.12em; color:#93c5fd;">Modo</span>
                    <span style="font-size:15px; font-weight:700;">Por kilo</span>
                </div>
            </div>

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

                                <div style="font-weight:600; font-size:11px; color:#05b302;">
                                    {{ ucfirst($prenda->tamano) }}
                                </div>

                                <div style="font-weight:600; font-size:8px; color:#b7b7b7;">
                                    {{ $prenda->descripcion }}
                                </div>

                                <div style="margin-top:10px; font-size:15px; font-weight:700; color:#0ea5e9;">
                                    Registro por kilo
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
                                font-size:10px;
                            ">
                            Registrar cliente en otra pestaña
                        </a>
                    @endif
                </div>

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
                                <span style="font-size:13px; color:#9ca3af;">
                                    Registro
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
                    @if ($this->descuentoGlobalActivo)
                        <div
                            style="
                                display:inline-flex;
                                align-items:center;
                                gap:8px;
                                padding:8px 12px;
                                margin-bottom:12px;
                                border-radius:999px;
                                background:rgba(34,197,94,.14);
                                color:#86efac;
                                border:1px solid rgba(34,197,94,.35);
                                font-size:13px;
                                font-weight:800;
                            ">
                            Descuento global activo
                            @if ($this->etiquetaDescuento)
                                <span style="opacity:.9;">{{ $this->etiquetaDescuento }}</span>
                            @endif
                        </div>
                    @endif

                    <div style="font-size:14px; color:#9ca3af; margin-bottom:4px;">
                        Total estimado actual
                    </div>

                    <div style="font-size:42px; font-weight:800; color:#22c55e;">
                        ${{ number_format($this->totalConDescuento, 2) }}
                    </div>

                    @if ($this->montoDescuento > 0)
                        <div style="font-size:12px; color:#9ca3af; margin-top:6px;">
                            Antes: ${{ number_format($total, 2) }} · Descuento: -${{ number_format($this->montoDescuento, 2) }}
                        </div>
                    @endif

                    <div style="font-size:12px; color:#9ca3af; margin-top:6px;">
                        Se define al cobrar con kilos y tipo de lavado
                    </div>

                    @if ($clienteSeleccionadoId)
                        <button type="button" wire:click="abrirModalCobro"
                            style="
                                width:100%;
                                padding:10px;
                                border-radius:13px;
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
                    max-width:820px;
                    max-height:calc(100vh - 32px);
                    overflow:auto;
                    background:white;
                    border-radius:18px;
                    padding:24px;
                    box-shadow:0 25px 50px rgba(0,0,0,.35);
                ">
                <div style="font-size:22px; font-weight:700; margin-bottom:18px; color:#111827;">
                    Registrar pago por kilo
                </div>

                <div style="display:grid; grid-template-columns:1.05fr .95fr; gap:18px;">
                    <div>
                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                                Kilos
                            </label>

                            <input type="number" step="0.01" min="0" wire:model.live="kilos"
                                style="
                                    width:100%;
                                    padding:12px;
                                    border-radius:10px;
                                    border:1px solid #d1d5db;
                                    color:#111827;
                                " />
                        </div>

                        <div style="margin-bottom:16px;">
                            <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                                Tipo de lavado
                            </label>

                            <select wire:model.live="tipoLavado"
                                style="
                                    width:100%;
                                    padding:12px;
                                    border-radius:10px;
                                    border:1px solid #d1d5db;
                                    color:#111827;
                                ">
                                @foreach ($this->tiposLavado as $tipoLavado)
                                    <option value="{{ $tipoLavado->clave }}">
                                        {{ $tipoLavado->nombre }} (${{ number_format((float) $tipoLavado->precio, 2) }}/kg)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div
                            style="
                                margin-bottom:18px;
                                padding:14px;
                                border-radius:12px;
                                background:#f3f4f6;
                                color:#111827;
                            ">
                            @if ($this->descuentoGlobalActivo)
                                <div
                                    style="
                                        display:inline-flex;
                                        align-items:center;
                                        gap:8px;
                                        margin-bottom:12px;
                                        padding:6px 10px;
                                        border-radius:999px;
                                        background:#ecfdf5;
                                        color:#166534;
                                        border:1px solid #bbf7d0;
                                        font-size:13px;
                                        font-weight:800;
                                    ">
                                    Descuento global activo
                                    @if ($this->etiquetaDescuento)
                                        <span>{{ $this->etiquetaDescuento }}</span>
                                    @endif
                                </div>
                            @endif

                            <div style="font-size:14px; color:#6b7280;">Precio actual por kilo</div>
                            <div style="font-size:20px; font-weight:700;">
                                ${{ number_format($this->getPrecioPorKilo(), 2) }}
                            </div>

                            <div style="font-size:14px; color:#6b7280; margin-top:10px;">Total a pagar</div>
                            <div style="font-size:24px; font-weight:800; color:#16a34a;">
                                ${{ number_format($this->totalConDescuento, 2) }}
                            </div>

                            @if ($this->montoDescuento > 0)
                                <div style="font-size:12px; color:#6b7280; margin-top:6px;">
                                    Antes: ${{ number_format($total, 2) }} · Descuento: -${{ number_format($this->montoDescuento, 2) }}
                                </div>
                            @endif
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
                    </div>

                    <div>
                        <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:14px; margin-bottom:16px;">
                            <div>
                                <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                                    Monto
                                </label>

                                <input type="number" step="0.01" min="0" wire:model.live.debounce.1000ms="montoTemporal"
                                    style="
                                        width:100%;
                                        padding:12px;
                                        border-radius:10px;
                                        border:1px solid #d1d5db;
                                        color:#111827;
                                    " />
                            </div>

                            <div>
                                <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                                    Con cuánto paga
                                </label>

                                <input type="number" step="0.01" min="0" wire:model.live.debounce.1000ms="montoRecibido"
                                    style="
                                        width:100%;
                                        padding:12px;
                                        border-radius:10px;
                                        border:1px solid #d1d5db;
                                        color:#111827;
                                    " />
                            </div>
                        </div>

                        <div style="margin-bottom:16px;">
                            <div style="font-size:14px; font-weight:700; color:#166534;">
                                Cambio a devolver: ${{ number_format((float) ($montoCambio ?? 0), 2) }}
                            </div>
                        </div>

                        <div style="margin-bottom:16px;">
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

                        <div style="margin-bottom:20px;">
                            <label
                                style="
                                    display:flex;
                                    gap:12px;
                                    align-items:flex-start;
                                    padding:14px;
                                    border-radius:12px;
                                    border:1px solid #d1d5db;
                                    background:#f9fafb;
                                    cursor:pointer;
                                ">
                                <input type="checkbox" wire:model.live="crearCuentaNueva"
                                    style="
                                        width:20px;
                                        height:20px;
                                        margin-top:2px;
                                        accent-color:#2563eb;
                                        cursor:pointer;
                                    ">

                                <div>
                                    <div style="font-weight:700; color:#111827;">
                                        Crear cuenta nueva
                                    </div>
                                    <div style="font-size:13px; color:#6b7280; margin-top:4px;">
                                        Si no marcas esta opción, el ticket se agregará a la cuenta abierta de hoy del cliente.
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:6px;">
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

                    <button type="button" wire:click="confirmarCobro" wire:loading.attr="disabled"
                        wire:target="confirmarCobro"
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
