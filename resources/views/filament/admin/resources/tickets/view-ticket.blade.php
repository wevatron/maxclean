<x-filament-panels::page>
    <style>
        .ticket-wrap {
            max-width: 1100px;
            margin: auto;
            padding: 30px;
        }

        .ticket-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .ticket-card {
            background: #1f446f;
            padding: 25px;
            border-radius: 16px;
            border: 1px solid #2c5d94;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        }

        .ticket-table-wrap {
            overflow-x: auto;
        }

        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            color: #e6edf5;
            min-width: 760px;
        }

        .ticket-table th,
        .ticket-table td {
            padding: 12px 8px;
            vertical-align: top;
        }

        .ticket-table thead tr {
            text-align: left;
            border-bottom: 1px solid #2c5d94;
            color: #9fb3c8;
        }

        .ticket-table tbody tr {
            border-bottom: 1px solid #244d7d;
        }

        .mobile-items {
            display: none;
        }

        .mobile-item-card {
            background: #163252;
            border: 1px solid #2c5d94;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 14px;
            color: #e6edf5;
        }

        .mobile-item-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
        }

        .mobile-item-label {
            color: #9fb3c8;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .aplica-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .aplica-badge-express {
            background: #7c2d12;
            color: #fdba74;
        }

        .aplica-badge-paquete {
            background: #1e3a5f;
            color: #bfdbfe;
        }

        .aplica-badge-normal {
            background: #1e293b;
            color: #cbd5e1;
        }

        .aplica-badge-kilo {
            background: #064e3b;
            color: #a7f3d0;
        }

        .aplica-badge-servicio {
            background: #312e81;
            color: #c4b5fd;
        }

        .explicacion-texto {
            margin-top: 6px;
            font-size: 12px;
            color: #9fb3c8;
            line-height: 1.5;
        }

        .inventory-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .inventory-btn {
            border: none;
            border-radius: 8px;
            padding: 6px 10px;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .inventory-btn-plus {
            background: #16a34a;
        }

        .inventory-btn-minus {
            background: #d97706;
        }

        .inventory-btn-delete {
            background: #b91c1c;
        }

        .inventory-note {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #143a2b;
            border: 1px solid #16a34a;
            color: #bbf7d0;
        }

        .service-note {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #22195a;
            border: 1px solid #7c3aed;
            color: #ddd6fe;
        }

        @media (max-width: 768px) {
            .ticket-wrap {
                padding: 16px;
            }

            .ticket-grid-2 {
                grid-template-columns: 1fr;
                gap: 16px;
                margin-bottom: 24px;
            }

            .ticket-header {
                display: block !important;
            }

            .ticket-header-status {
                margin-top: 16px;
            }

            .desktop-items {
                display: none;
            }

            .mobile-items {
                display: block;
            }

            .ticket-card {
                padding: 18px;
            }

            .ticket-title {
                font-size: 26px !important;
            }

            .pago-row {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 8px;
            }
        }
    </style>

    <div class="ticket-wrap">
        @php
            $esAutoservicio = $record->tipo === 'autoservicio';

            $tipoTicket = match ($record->tipo) {
                'encargo_express' => 'ENCARGO EXPRESS',
                'encargo_kilo' => 'POR KILO',
                'autoservicio' => 'AUTOSERVICIO',
                default => 'ENCARGO',
            };

            $tipoBg = match ($record->tipo) {
                'encargo_express' => '#7c2d12',
                'encargo_kilo' => '#064e3b',
                'autoservicio' => '#312e81',
                default => '#1e3a5f',
            };

            $tipoColor = match ($record->tipo) {
                'encargo_express' => '#fdba74',
                'encargo_kilo' => '#a7f3d0',
                'autoservicio' => '#c4b5fd',
                default => '#bfdbfe',
            };

            $tipoTexto = match ($record->tipo) {
                'encargo_express' => 'Encargo express',
                'encargo_kilo' => 'Por kilo',
                'autoservicio' => 'Autoservicio / renta de máquinas',
                default => 'Encargo',
            };

            $tipoLavadoTexto = match ($record->tipo_lavado_kilo ?? null) {
                'basico' => 'Básico',
                'premium' => 'Premium',
                'extra_lavado' => 'Extra lavado',
                'expres' => 'Expres',
                'ropa_interior' => 'Ropa interior',
                default => 'Sin especificar',
            };
        @endphp

        <div class="ticket-header"
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:40px; gap:20px; flex-wrap:wrap;">

            <div>
                <h1 class="ticket-title" style="font-size:32px; font-weight:800; color:#ffffff; margin:0;">
                    Ticket #{{ $record->numero }}
                </h1>

                <div style="margin-top:8px; color:#9fb3c8;">
                    {{ $record->created_at->format('d/m/Y H:i') }}
                </div>

                <div style="display:flex; gap:10px; margin-top:14px; flex-wrap:wrap;">
                    <span
                        style="
                            background:{{ $tipoBg }};
                            color:{{ $tipoColor }};
                            padding:8px 14px;
                            border-radius:999px;
                            font-weight:700;
                            font-size:13px;
                        ">
                        {{ $tipoTicket }}
                    </span>

                    <span
                        style="
                            background:#1e293b;
                            color:#cbd5e1;
                            padding:8px 14px;
                            border-radius:999px;
                            font-weight:700;
                            font-size:13px;
                        ">
                        {{ strtoupper($record->status->nombre ?? 'SIN ESTADO') }}
                    </span>
                </div>
            </div>

            <div class="ticket-header-status">
                @if ($record->saldo > 0)
                    <span
                        style="
                            background:#5a1f1f;
                            color:#ffb4b4;
                            padding:10px 20px;
                            border-radius:999px;
                            font-weight:700;
                        ">
                        PENDIENTE
                    </span>
                @else
                    <span
                        style="
                            background:#1f5a36;
                            color:#b9ffd9;
                            padding:10px 20px;
                            border-radius:999px;
                            font-weight:700;
                        ">
                        PAGADO
                    </span>
                @endif
            </div>
        </div>

        <div class="ticket-grid-2">
            <div class="ticket-card">
                <h3 style="font-weight:700; margin-bottom:15px; color:#ffffff;">
                    Información
                </h3>

                <p style="color:#e6edf5; margin:0 0 10px 0;">
                    <strong>Cliente:</strong>
                    {{ $record->cliente->name ?? 'Sin cliente' }}
                </p>

                <p style="color:#e6edf5; margin:0 0 10px 0;">
                    <strong>Operador:</strong>
                    {{ $record->operador->name ?? 'Sin operador' }}
                </p>

                <p style="color:#e6edf5; margin:0 0 10px 0;">
                    <strong>Sucursal:</strong>
                    {{ $record->sucursal->nombre ?? 'Sin sucursal' }}
                </p>

                <p style="color:#e6edf5; margin:0 0 10px 0;">
                    <strong>Estado:</strong>
                    {{ $record->status->nombre ?? 'Sin estado' }}
                </p>

                <p style="color:#e6edf5; margin:0 0 10px 0;">
                    <strong>Tipo:</strong>
                    {{ $tipoTexto }}
                </p>

                @if ($record->tipo === 'encargo_kilo')
                    <p style="color:#e6edf5; margin:10px 0 0 0;">
                        <strong>Kilos:</strong>
                        {{ number_format((float) $record->kilos, 2) }} kg
                    </p>

                    <p style="color:#e6edf5; margin:10px 0 0 0;">
                        <strong>Tipo de lavado:</strong>
                        {{ $tipoLavadoTexto }}
                    </p>

                    <p style="color:#e6edf5; margin:10px 0 0 0;">
                        <strong>Precio por kilo:</strong>
                        ${{ number_format((float) $record->precio_kilo, 2) }}
                    </p>
                @endif
            </div>

            <div class="ticket-card">
                <h3 style="font-weight:700; margin-bottom:15px; color:#ffffff;">
                    Resumen
                </h3>

                <p style="color:#e6edf5; margin:0 0 10px 0;">
                    <strong>Total:</strong> ${{ number_format($record->total, 2) }}
                </p>

                <p style="color:#7dffb5; margin:0 0 10px 0;">
                    <strong>Pagado:</strong> ${{ number_format($record->pagado, 2) }}
                </p>

                <p style="color:{{ $record->saldo > 0 ? '#ff8a8a' : '#7dffb5' }}; margin:0;">
                    <strong>Saldo:</strong> ${{ number_format($record->saldo, 2) }}
                </p>
            </div>
        </div>

        @if ($esAutoservicio)
            <div class="ticket-card" style="margin-bottom:40px;">
                <div
                    style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:20px;">
                    <h3 style="font-weight:700; margin:0; color:#ffffff;">
                        Servicios
                    </h3>

                    <button type="button" wire:click="abrirModalAgregarServicio"
                        style="
                            background:#16a34a;
                            color:#ffffff;
                            border:none;
                            padding:10px 14px;
                            border-radius:10px;
                            font-size:13px;
                            font-weight:700;
                            cursor:pointer;
                        ">
                        Agregar servicio
                    </button>
                </div>

                <div class="service-note">
                    Los servicios agregados aquí sí modifican el total del ticket y su saldo pendiente.
                </div>

                <div class="desktop-items ticket-table-wrap">
                    <table class="ticket-table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Aplicación</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($record->servicios as $servicio)
                                @php
                                    $cantidad = (int) ($servicio->pivot->cantidad ?? 1);
                                    $precioUnitario =
                                        (float) ($servicio->pivot->precio_unitario ?? ($servicio->precio_base ?? 0));
                                    $subtotal = (float) ($servicio->pivot->subtotal ?? $cantidad * $precioUnitario);
                                @endphp

                                <tr>
                                    <td>
                                        <div style="font-weight:600;">
                                            {{ $servicio->nombre ?? 'Sin servicio' }}
                                        </div>

                                        @if (!empty($servicio->descripcion))
                                            <div class="explicacion-texto">
                                                {{ $servicio->descripcion }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>{{ $cantidad }}</td>

                                    <td>${{ number_format($precioUnitario, 2) }}</td>

                                    <td>
                                        <span class="aplica-badge aplica-badge-servicio">
                                            Servicio / ciclo
                                        </span>

                                        <div class="explicacion-texto">
                                            {{ $cantidad }} × ${{ number_format($precioUnitario, 2) }}
                                            = ${{ number_format($subtotal, 2) }}
                                        </div>
                                    </td>

                                    <td style="font-weight:600;">
                                        ${{ number_format($subtotal, 2) }}
                                    </td>

                                    <td>
                                        <div class="inventory-actions">
                                            <button type="button"
                                                wire:click="incrementarServicioTicket({{ $servicio->id }})"
                                                class="inventory-btn inventory-btn-plus">
                                                +
                                            </button>

                                            <button type="button"
                                                wire:click="disminuirServicioTicket({{ $servicio->id }})"
                                                class="inventory-btn inventory-btn-minus">
                                                -
                                            </button>

                                            <button type="button"
                                                wire:click="quitarServicioTicket({{ $servicio->id }})"
                                                class="inventory-btn inventory-btn-delete">
                                                Quitar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="color:#9fb3c8;">
                                        No hay servicios registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mobile-items">
                    @forelse ($record->servicios as $servicio)
                        @php
                            $cantidad = (int) ($servicio->pivot->cantidad ?? 1);
                            $precioUnitario =
                                (float) ($servicio->pivot->precio_unitario ?? ($servicio->precio_base ?? 0));
                            $subtotal = (float) ($servicio->pivot->subtotal ?? $cantidad * $precioUnitario);
                        @endphp

                        <div class="mobile-item-card">
                            <div style="font-size:18px; font-weight:700; margin-bottom:12px;">
                                {{ $servicio->nombre ?? 'Sin servicio' }}
                            </div>

                            @if (!empty($servicio->descripcion))
                                <div class="explicacion-texto" style="margin-bottom:12px;">
                                    {{ $servicio->descripcion }}
                                </div>
                            @endif

                            <div class="mobile-item-row">
                                <div>
                                    <div class="mobile-item-label">Cantidad</div>
                                    <div>{{ $cantidad }}</div>
                                </div>

                                <div style="text-align:right;">
                                    <div class="mobile-item-label">Precio unitario</div>
                                    <div>${{ number_format($precioUnitario, 2) }}</div>
                                </div>
                            </div>

                            <div style="margin-top:10px;">
                                <div class="mobile-item-label">Aplicación</div>

                                <span class="aplica-badge aplica-badge-servicio">
                                    Servicio / ciclo
                                </span>

                                <div class="explicacion-texto">
                                    {{ $cantidad }} × ${{ number_format($precioUnitario, 2) }}
                                    = ${{ number_format($subtotal, 2) }}
                                </div>
                            </div>

                            <div style="margin-top:14px;">
                                <div class="mobile-item-label">Subtotal</div>
                                <div style="font-size:18px; font-weight:800;">
                                    ${{ number_format($subtotal, 2) }}
                                </div>
                            </div>

                            <div style="margin-top:14px;">
                                <div class="mobile-item-label">Acciones</div>

                                <div class="inventory-actions" style="margin-top:8px;">
                                    <button type="button"
                                        wire:click="incrementarServicioTicket({{ $servicio->id }})"
                                        class="inventory-btn inventory-btn-plus">
                                        +
                                    </button>

                                    <button type="button"
                                        wire:click="disminuirServicioTicket({{ $servicio->id }})"
                                        class="inventory-btn inventory-btn-minus">
                                        -
                                    </button>

                                    <button type="button"
                                        wire:click="quitarServicioTicket({{ $servicio->id }})"
                                        class="inventory-btn inventory-btn-delete">
                                        Quitar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            style="
                                color:#9fb3c8;
                                background:#163252;
                                border:1px solid #2c5d94;
                                padding:14px;
                                border-radius:12px;
                            ">
                            No hay servicios registrados.
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            <div class="ticket-card" style="margin-bottom:40px;">
                <div
                    style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:20px;">
                    <h3 style="font-weight:700; margin:0; color:#ffffff;">
                        Prendas
                    </h3>

                    @if ($record->tipo === 'encargo_kilo')
                        <button type="button" wire:click="abrirModalAgregarPrenda"
                            style="
                                background:#16a34a;
                                color:#ffffff;
                                border:none;
                                padding:10px 14px;
                                border-radius:10px;
                                font-size:13px;
                                font-weight:700;
                                cursor:pointer;
                            ">
                            Agregar prendas al inventario
                        </button>
                    @endif
                </div>

                @if ($record->tipo === 'encargo_kilo')
                    <div class="inventory-note">
                        Este ticket se cobró por peso. Las prendas de esta sección se registran después del lavado como
                        inventario y no modifican el total.
                    </div>
                @endif

                <div class="desktop-items ticket-table-wrap">
                    <table class="ticket-table">
                        <thead>
                            <tr>
                                <th>Prenda</th>
                                <th>Cantidad</th>
                                <th>Precio base</th>
                                <th>Aplicación</th>
                                <th>Subtotal</th>
                                @if ($record->tipo === 'encargo_kilo')
                                    <th>Acciones</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($record->items as $item)
                                @php
                                    $precioConfig = $item->prenda
                                        ?->precios()
                                        ->where('sucursal_id', $record->sucursal_id)
                                        ->first();

                                    $precioNormal =
                                        (float) ($precioConfig?->precio_normal ?? ($item->precio_unitario ?? 0));
                                    $precioExpress = (float) ($precioConfig?->precio_express ?? 0);
                                    $precioPaquete = (float) ($precioConfig?->precio_paquete ?? 0);
                                    $piezasPorPaquete = (int) ($precioConfig?->piezas_por_paquete ?? 0);
                                    $cantidad = (int) $item->cantidad;

                                    $aplicacionTitulo = 'Precio normal';
                                    $aplicacionClase = 'aplica-badge-normal';
                                    $explicacion = 'Se cobró con precio regular por pieza.';

                                    if ($record->tipo === 'encargo_kilo') {
                                        $aplicacionTitulo = 'Por kilo';
                                        $aplicacionClase = 'aplica-badge-kilo';
                                        $explicacion = 'Prenda registrada solo como control.';
                                    } elseif ($record->tipo === 'encargo_express') {
                                        $aplicacionTitulo = 'Modo express';
                                        $aplicacionClase = 'aplica-badge-express';

                                        if ($precioExpress > 0) {
                                            $explicacion =
                                                $cantidad .
                                                ' × $' .
                                                number_format($precioExpress, 2) .
                                                ' = $' .
                                                number_format($item->subtotal, 2);
                                        } else {
                                            $explicacion =
                                                'El ticket está en modo express, pero no se encontró precio express actual; se muestra el subtotal guardado.';
                                        }
                                    } else {
                                        if ($piezasPorPaquete > 0 && $precioPaquete > 0) {
                                            $paquetes = intdiv($cantidad, $piezasPorPaquete);
                                            $sueltas = $cantidad % $piezasPorPaquete;

                                            if ($paquetes > 0) {
                                                $aplicacionTitulo = 'Combo / paquete';
                                                $aplicacionClase = 'aplica-badge-paquete';

                                                $partes = [];

                                                $partes[] =
                                                    $paquetes .
                                                    ' paquete(s) de ' .
                                                    $piezasPorPaquete .
                                                    ' por $' .
                                                    number_format($precioPaquete, 2);

                                                if ($sueltas > 0) {
                                                    $partes[] =
                                                        $sueltas . ' suelta(s) a $' . number_format($precioNormal, 2);
                                                }

                                                $explicacion =
                                                    implode(' + ', $partes) .
                                                    ' = $' .
                                                    number_format($item->subtotal, 2);
                                            } else {
                                                $explicacion =
                                                    $cantidad .
                                                    ' × $' .
                                                    number_format($precioNormal, 2) .
                                                    ' = $' .
                                                    number_format($item->subtotal, 2);
                                            }
                                        } else {
                                            $explicacion =
                                                $cantidad .
                                                ' × $' .
                                                number_format($precioNormal, 2) .
                                                ' = $' .
                                                number_format($item->subtotal, 2);
                                        }
                                    }
                                @endphp

                                <tr>
                                    <td>
                                        <div style="font-weight:600;">
                                            {{ $item->prenda->nombre ?? 'Sin prenda' }}
                                        </div>
                                    </td>

                                    <td>{{ $item->cantidad }}</td>

                                    <td>${{ number_format($item->precio_unitario, 2) }}</td>

                                    <td>
                                        <span class="aplica-badge {{ $aplicacionClase }}">
                                            {{ $aplicacionTitulo }}
                                        </span>

                                        <div class="explicacion-texto">
                                            {{ $explicacion }}
                                        </div>
                                    </td>

                                    <td style="font-weight:600;">
                                        ${{ number_format($item->subtotal, 2) }}
                                    </td>

                                    @if ($record->tipo === 'encargo_kilo')
                                        <td>
                                            <div class="inventory-actions">
                                                <button type="button"
                                                    wire:click="incrementarItemInventario({{ $item->id }})"
                                                    class="inventory-btn inventory-btn-plus">
                                                    +
                                                </button>

                                                <button type="button"
                                                    wire:click="disminuirItemInventario({{ $item->id }})"
                                                    class="inventory-btn inventory-btn-minus">
                                                    -
                                                </button>

                                                <button type="button"
                                                    wire:click="quitarItemInventario({{ $item->id }})"
                                                    class="inventory-btn inventory-btn-delete">
                                                    Quitar
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $record->tipo === 'encargo_kilo' ? 6 : 5 }}"
                                        style="color:#9fb3c8;">
                                        @if ($record->tipo === 'encargo_kilo')
                                            Aún no se han inventariado prendas para este ticket por kilo.
                                        @else
                                            No hay prendas registradas.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mobile-items">
                    @forelse ($record->items as $item)
                        @php
                            $precioConfig = $item->prenda
                                ?->precios()
                                ->where('sucursal_id', $record->sucursal_id)
                                ->first();

                            $precioNormal = (float) ($precioConfig?->precio_normal ?? ($item->precio_unitario ?? 0));
                            $precioExpress = (float) ($precioConfig?->precio_express ?? 0);
                            $precioPaquete = (float) ($precioConfig?->precio_paquete ?? 0);
                            $piezasPorPaquete = (int) ($precioConfig?->piezas_por_paquete ?? 0);
                            $cantidad = (int) $item->cantidad;

                            $aplicacionTitulo = 'Precio normal';
                            $aplicacionClase = 'aplica-badge-normal';
                            $explicacion = 'Se cobró con precio regular por pieza.';

                            if ($record->tipo === 'encargo_kilo') {
                                $aplicacionTitulo = 'Por kilo';
                                $aplicacionClase = 'aplica-badge-kilo';
                                $explicacion = 'Prenda registrada solo como control';
                            } elseif ($record->tipo === 'encargo_express') {
                                $aplicacionTitulo = 'Modo express';
                                $aplicacionClase = 'aplica-badge-express';

                                if ($precioExpress > 0) {
                                    $explicacion =
                                        $cantidad .
                                        ' × $' .
                                        number_format($precioExpress, 2) .
                                        ' = $' .
                                        number_format($item->subtotal, 2);
                                } else {
                                    $explicacion =
                                        'El ticket está en modo express, pero no se encontró precio express actual; se muestra el subtotal guardado.';
                                }
                            } else {
                                if ($piezasPorPaquete > 0 && $precioPaquete > 0) {
                                    $paquetes = intdiv($cantidad, $piezasPorPaquete);
                                    $sueltas = $cantidad % $piezasPorPaquete;

                                    if ($paquetes > 0) {
                                        $aplicacionTitulo = 'Combo / paquete';
                                        $aplicacionClase = 'aplica-badge-paquete';

                                        $partes = [];
                                        $partes[] =
                                            $paquetes .
                                            ' paquete(s) de ' .
                                            $piezasPorPaquete .
                                            ' por $' .
                                            number_format($precioPaquete, 2);

                                        if ($sueltas > 0) {
                                            $partes[] = $sueltas . ' suelta(s) a $' . number_format($precioNormal, 2);
                                        }

                                        $explicacion =
                                            implode(' + ', $partes) . ' = $' . number_format($item->subtotal, 2);
                                    } else {
                                        $explicacion =
                                            $cantidad .
                                            ' × $' .
                                            number_format($precioNormal, 2) .
                                            ' = $' .
                                            number_format($item->subtotal, 2);
                                    }
                                } else {
                                    $explicacion =
                                        $cantidad .
                                        ' × $' .
                                        number_format($precioNormal, 2) .
                                        ' = $' .
                                        number_format($item->subtotal, 2);
                                }
                            }
                        @endphp

                        <div class="mobile-item-card">
                            <div style="font-size:18px; font-weight:700; margin-bottom:12px;">
                                {{ $item->prenda->nombre ?? 'Sin prenda' }}
                            </div>

                            <div class="mobile-item-row">
                                <div>
                                    <div class="mobile-item-label">Cantidad</div>
                                    <div>{{ $item->cantidad }}</div>
                                </div>

                                <div style="text-align:right;">
                                    <div class="mobile-item-label">Precio base</div>
                                    <div>${{ number_format($item->precio_unitario, 2) }}</div>
                                </div>
                            </div>

                            <div style="margin-top:10px;">
                                <div class="mobile-item-label">Aplicación</div>
                                <span class="aplica-badge {{ $aplicacionClase }}">
                                    {{ $aplicacionTitulo }}
                                </span>

                                <div class="explicacion-texto">
                                    {{ $explicacion }}
                                </div>
                            </div>

                            <div style="margin-top:14px;">
                                <div class="mobile-item-label">Subtotal</div>
                                <div style="font-size:18px; font-weight:800;">
                                    ${{ number_format($item->subtotal, 2) }}
                                </div>
                            </div>

                            @if ($record->tipo === 'encargo_kilo')
                                <div style="margin-top:14px;">
                                    <div class="mobile-item-label">Acciones</div>

                                    <div class="inventory-actions" style="margin-top:8px;">
                                        <button type="button"
                                            wire:click="incrementarItemInventario({{ $item->id }})"
                                            class="inventory-btn inventory-btn-plus">
                                            +
                                        </button>

                                        <button type="button"
                                            wire:click="disminuirItemInventario({{ $item->id }})"
                                            class="inventory-btn inventory-btn-minus">
                                            -
                                        </button>

                                        <button type="button" wire:click="quitarItemInventario({{ $item->id }})"
                                            class="inventory-btn inventory-btn-delete">
                                            Quitar
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div
                            style="
                                color:#9fb3c8;
                                background:#163252;
                                border:1px solid #2c5d94;
                                padding:14px;
                                border-radius:12px;
                            ">
                            @if ($record->tipo === 'encargo_kilo')
                                Aún no se han inventariado prendas para este ticket por kilo.
                            @else
                                No hay prendas registradas.
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <div class="ticket-card">
            <h3 style="font-weight:700; margin-bottom:20px; color:#ffffff;">
                Pagos
            </h3>

            @forelse($record->pagos as $pago)
                @php
                    $esCancelado = $pago->metodo_pago === 'cancelado';
                    $colorFondo = $esCancelado ? '#2a1b1b' : '#163252';
                    $colorBorde = $esCancelado ? '#7f1d1d' : '#2c5d94';
                    $colorMonto = $esCancelado ? '#ff8a8a' : '#7dffb5';
                @endphp

                <div class="pago-row"
                    style="
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        margin-bottom:12px;
                        padding:14px 16px;
                        border-radius:14px;
                        background:{{ $colorFondo }};
                        border:1px solid {{ $colorBorde }};
                        transition:all .2s ease;
                    ">
                    <div>
                        <div style="font-weight:700; font-size:15px; color:#ffffff;">
                            {{ ucfirst($pago->metodo_pago) }}
                        </div>

                        <div style="font-size:12px; color:#9fb3c8; margin-top:4px;">
                            {{ $pago->created_at?->format('d/m/Y H:i') }}
                        </div>

                        @if ($esCancelado)
                            <div style="margin-top:6px; font-size:11px; color:#ff8a8a;">
                                Este pago fue cancelado manualmente
                            </div>
                        @endif
                    </div>

                    <div style="display:flex; align-items:center; gap:18px;">
                        <span
                            style="
                                font-weight:800;
                                font-size:16px;
                                color: {{ $colorMonto }};
                            ">
                            {{ $pago->monto < 0 ? '-' : '+' }}
                            ${{ number_format(abs($pago->monto), 2) }}
                        </span>

                        @if (!$esCancelado)
                            <button wire:click="confirmarCancelacion({{ $pago->id }})"
                                style="
                                    background:#7f1d1d;
                                    border:1px solid #b91c1c;
                                    color:#ffffff;
                                    padding:6px 12px;
                                    border-radius:8px;
                                    font-size:12px;
                                    font-weight:600;
                                    cursor:pointer;
                                    transition:all .2s ease;
                                "
                                onmouseover="this.style.background='#991b1b'"
                                onmouseout="this.style.background='#7f1d1d'">
                                Cancelar
                            </button>
                        @else
                            <span
                                style="
                                    background:#3f3f46;
                                    border:1px solid #52525b;
                                    color:#d4d4d8;
                                    padding:5px 10px;
                                    border-radius:6px;
                                    font-size:11px;
                                    font-weight:700;
                                ">
                                CANCELADO
                            </span>
                        @endif
                    </div>
                </div>

            @empty
                <div
                    style="
                        color:#9fb3c8;
                        background:#163252;
                        border:1px solid #2c5d94;
                        padding:14px;
                        border-radius:12px;
                    ">
                    No hay pagos registrados.
                </div>
            @endforelse
        </div>

        @if (!$esAutoservicio)
            <div class="ticket-card" style="margin-top:40px;">
                <h3 style="font-weight:700; margin-bottom:20px; color:#ffffff;">
                    Procesos
                </h3>

                @php
                    $ordenProcesos = \App\Models\Ticket::ordenProcesos();

                    $procesosOrdenados = $record->procesos->sortBy(function ($p) use ($ordenProcesos) {
                        return array_search($p->proceso, $ordenProcesos);
                    });
                @endphp

                @forelse($procesosOrdenados as $proceso)
                    @php
                        $completado = $proceso->completado;
                    @endphp

                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            padding:12px 16px;
                            margin-bottom:10px;
                            border-radius:12px;
                            background: {{ $completado ? '#143a2b' : '#1e293b' }};
                            border: 1px solid {{ $completado ? '#16a34a' : '#334155' }};
                        ">

                        <div style="font-weight:600; color:#ffffff;">
                            {{ ucfirst($proceso->proceso) }}
                        </div>

                        <div style="display:flex; gap:12px; align-items:center;">
                            @php
                                $ordenProcesos = \App\Models\Ticket::ordenProcesos();
                                $indexActual = array_search($proceso->proceso, $ordenProcesos);

                                $puedeMarcar = true;

                                if ($indexActual > 0) {
                                    $procesoAnterior = $ordenProcesos[$indexActual - 1];

                                    $puedeMarcar = $record->procesos
                                        ->where('proceso', $procesoAnterior)
                                        ->where('completado', true)
                                        ->isNotEmpty();
                                }
                            @endphp

                            @if (!$completado)
                                <button
                                    @if ($puedeMarcar) wire:click="confirmarProceso({{ $proceso->id }})"
                                    @else
                                        disabled @endif
                                    style="
                                        background: {{ $puedeMarcar ? '#16a34a' : '#334155' }};
                                        color:#fff;
                                        padding:6px 12px;
                                        border-radius:8px;
                                        font-size:12px;
                                        border:none;
                                        cursor: {{ $puedeMarcar ? 'pointer' : 'not-allowed' }};
                                    ">
                                    Marcar completado
                                </button>
                            @else
                                <span
                                    style="
                                        background:#065f46;
                                        color:#bbf7d0;
                                        padding:6px 10px;
                                        border-radius:8px;
                                        font-size:12px;
                                        font-weight:700;
                                    ">
                                    COMPLETADO
                                </span>
                            @endif
                        </div>
                    </div>

                @empty
                    <div style="color:#9fb3c8;">
                        No hay procesos registrados.
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    @if ($modalAgregarPrendaAbierto)
        <div
            style="
            position:fixed;
            inset:0;
            background:rgba(0,0,0,.6);
            display:flex;
            align-items:center;
            justify-content:center;
            z-index:9999;
            padding:20px;
        ">
            <div
                style="
                width:100%;
                max-width:650px;
                background:white;
                border-radius:18px;
                padding:24px;
                box-shadow:0 25px 50px rgba(0,0,0,.35);
            ">
                <div style="font-size:22px; font-weight:700; margin-bottom:18px; color:#111827;">
                    Agregar prendas al inventario
                </div>

                <div style="margin-bottom:14px; color:#4b5563; font-size:14px;">
                    Estas prendas se registran solo como inventario del ticket por kilo y no cambian el total cobrado.
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                        Buscar y seleccionar prenda
                    </label>

                    <input type="text" wire:model.live.debounce.300ms="buscarPrenda"
                        placeholder="Escribe nombre, descripción o tamaño..."
                        style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        color:#111827;
                    " />
                </div>

                @if ($prendaSeleccionadaId)
                    <div
                        style="
                        margin-bottom:16px;
                        padding:12px 14px;
                        border-radius:12px;
                        background:#ecfdf5;
                        border:1px solid #86efac;
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        gap:12px;
                    ">
                        <div>
                            <div style="font-size:12px; color:#166534; font-weight:700;">
                                PRENDA SELECCIONADA
                            </div>
                            <div style="font-size:15px; color:#111827; font-weight:600;">
                                {{ $prendaSeleccionadaTexto }}
                            </div>
                        </div>

                        <button type="button" wire:click="limpiarSeleccionPrenda"
                            style="
                            border:none;
                            background:#ef4444;
                            color:white;
                            padding:8px 12px;
                            border-radius:8px;
                            cursor:pointer;
                            font-weight:700;
                        ">
                            Cambiar
                        </button>
                    </div>
                @endif

                @if (!$prendaSeleccionadaId && filled($buscarPrenda))
                    <div
                        style="
                        margin-bottom:16px;
                        border:1px solid #d1d5db;
                        border-radius:12px;
                        max-height:240px;
                        overflow-y:auto;
                        background:white;
                    ">
                        @forelse ($this->prendasDisponibles as $prenda)
                            <button type="button" wire:click="seleccionarPrendaInventario({{ $prenda->id }})"
                                style="
                                width:100%;
                                text-align:left;
                                padding:12px 14px;
                                border:none;
                                background:white;
                                border-bottom:1px solid #e5e7eb;
                                cursor:pointer;
                                color:#111827;
                            ">
                                <div style="font-weight:700;">
                                    {{ $prenda->nombre }}
                                </div>

                                <div style="font-size:12px; color:#6b7280; margin-top:4px;">
                                    @if (!empty($prenda->tamano))
                                        {{ ucfirst($prenda->tamano) }}
                                    @endif

                                    @if (!empty($prenda->descripcion))
                                        @if (!empty($prenda->tamano))
                                            ·
                                        @endif
                                        {{ $prenda->descripcion }}
                                    @endif
                                </div>
                            </button>
                        @empty
                            <div style="padding:14px; color:#6b7280;">
                                No se encontraron prendas con esa búsqueda.
                            </div>
                        @endforelse
                    </div>
                @endif

                <div style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                        Cantidad
                    </label>

                    <input type="number" min="1" wire:model="cantidadPrenda"
                        style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        color:#111827;
                    " />
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" wire:click="cerrarModalAgregarPrenda"
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

                    <button type="button" wire:click="agregarPrendaInventario"
                        style="
                        padding:12px 16px;
                        border:none;
                        border-radius:10px;
                        background:{{ $prendaSeleccionadaId ? '#16a34a' : '#9ca3af' }};
                        color:white;
                        cursor:{{ $prendaSeleccionadaId ? 'pointer' : 'not-allowed' }};
                    "
                        @if (!$prendaSeleccionadaId) disabled @endif>
                        Guardar prenda
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($modalAgregarServicioAbierto)
        <div
            style="
            position:fixed;
            inset:0;
            background:rgba(0,0,0,.6);
            display:flex;
            align-items:center;
            justify-content:center;
            z-index:9999;
            padding:20px;
        ">
            <div
                style="
                width:100%;
                max-width:650px;
                background:white;
                border-radius:18px;
                padding:24px;
                box-shadow:0 25px 50px rgba(0,0,0,.35);
            ">
                <div style="font-size:22px; font-weight:700; margin-bottom:18px; color:#111827;">
                    Agregar servicio al ticket
                </div>

                <div style="margin-bottom:14px; color:#4b5563; font-size:14px;">
                    Estos servicios sí modifican el total cobrado del ticket de autoservicio.
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                        Buscar y seleccionar servicio
                    </label>

                    <input type="text" wire:model.live.debounce.300ms="buscarServicio"
                        placeholder="Escribe nombre o descripción del servicio..."
                        style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        color:#111827;
                    " />
                </div>

                @if ($servicioSeleccionadoId)
                    <div
                        style="
                        margin-bottom:16px;
                        padding:12px 14px;
                        border-radius:12px;
                        background:#eef2ff;
                        border:1px solid #a5b4fc;
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        gap:12px;
                    ">
                        <div>
                            <div style="font-size:12px; color:#4338ca; font-weight:700;">
                                SERVICIO SELECCIONADO
                            </div>
                            <div style="font-size:15px; color:#111827; font-weight:600;">
                                {{ $servicioSeleccionadoTexto }}
                            </div>
                        </div>

                        <button type="button" wire:click="limpiarSeleccionServicio"
                            style="
                            border:none;
                            background:#ef4444;
                            color:white;
                            padding:8px 12px;
                            border-radius:8px;
                            cursor:pointer;
                            font-weight:700;
                        ">
                            Cambiar
                        </button>
                    </div>
                @endif

                @if (!$servicioSeleccionadoId && filled($buscarServicio))
                    <div
                        style="
                        margin-bottom:16px;
                        border:1px solid #d1d5db;
                        border-radius:12px;
                        max-height:240px;
                        overflow-y:auto;
                        background:white;
                    ">
                        @forelse ($this->serviciosDisponibles as $servicio)
                            <button type="button" wire:click="seleccionarServicioTicket({{ $servicio->id }})"
                                style="
                                width:100%;
                                text-align:left;
                                padding:12px 14px;
                                border:none;
                                background:white;
                                border-bottom:1px solid #e5e7eb;
                                cursor:pointer;
                                color:#111827;
                            ">
                                <div style="font-weight:700;">
                                    {{ $servicio->nombre }}
                                </div>

                                @if (!empty($servicio->descripcion))
                                    <div style="font-size:12px; color:#6b7280; margin-top:4px;">
                                        {{ $servicio->descripcion }}
                                    </div>
                                @endif
                            </button>
                        @empty
                            <div style="padding:14px; color:#6b7280;">
                                No se encontraron servicios con esa búsqueda.
                            </div>
                        @endforelse
                    </div>
                @endif

                <div style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:600; color:#111827;">
                        Cantidad
                    </label>

                    <input type="number" min="1" wire:model="cantidadServicio"
                        style="
                        width:100%;
                        padding:12px;
                        border-radius:10px;
                        border:1px solid #d1d5db;
                        color:#111827;
                    " />
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" wire:click="cerrarModalAgregarServicio"
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

                    <button type="button" wire:click="agregarServicioTicket"
                        style="
                        padding:12px 16px;
                        border:none;
                        border-radius:10px;
                        background:{{ $servicioSeleccionadoId ? '#16a34a' : '#9ca3af' }};
                        color:white;
                        cursor:{{ $servicioSeleccionadoId ? 'pointer' : 'not-allowed' }};
                    "
                        @if (!$servicioSeleccionadoId) disabled @endif>
                        Guardar servicio
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>