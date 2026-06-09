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

        .explicacion-texto {
            margin-top: 6px;
            font-size: 12px;
            color: #9fb3c8;
            line-height: 1.5;
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
            $tipoTicket = match ($record->tipo) {
                'encargo_express' => 'ENCARGO EXPRESS',
                'encargo_kilo' => 'POR KILO',
                default => 'ENCARGO',
            };

            $tipoBg = match ($record->tipo) {
                'encargo_express' => '#7c2d12',
                'encargo_kilo' => '#064e3b',
                default => '#1e3a5f',
            };

            $tipoColor = match ($record->tipo) {
                'encargo_express' => '#fdba74',
                'encargo_kilo' => '#a7f3d0',
                default => '#bfdbfe',
            };

            $tipoTexto = match ($record->tipo) {
                'encargo_express' => 'Encargo express',
                'encargo_kilo' => 'Por kilo',
                default => 'Encargo',
            };

            $tipoLavadoTexto = $record->tipo_lavado_kilo_label;
        @endphp
        {{-- HEADER --}}
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
                    {{--                     <span
                        style="
                        background:{{ $record->tipo === 'encargo_express' ? '#7c2d12' : '#1e3a5f' }};
                        color:{{ $record->tipo === 'encargo_express' ? '#fdba74' : '#bfdbfe' }};
                        padding:8px 14px;
                        border-radius:999px;
                        font-weight:700;
                        font-size:13px;
                    ">
                        {{ $record->tipo === 'encargo_express' ? 'ENCARGO EXPRESS' : 'ENCARGO' }}
                    </span> --}}

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

        {{-- INFO GENERAL --}}
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

                <p style="color:#e6edf5; margin:0;">
                    <strong>Tipo:</strong>
                    {{ $tipoTexto }}
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
                </p>
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

        {{-- ITEMS --}}
        <div class="ticket-card" style="margin-bottom:40px;">
            <h3 style="font-weight:700; margin-bottom:20px; color:#ffffff;">
                Prendas
            </h3>

            {{-- ESCRITORIO --}}
            <div class="desktop-items ticket-table-wrap">
                <table class="ticket-table">
                    <thead>
                        <tr>
                            <th>Prenda</th>
                            <th>Cantidad</th>
                            <th>Precio base</th>
                            <th>Aplicación</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($record->items as $item)
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
                                    $explicacion =
                                        'Prenda registrada solo como control. El cobro fue por ' .
                                        number_format((float) $record->kilos, 2) .
                                        ' kg × $' .
                                        number_format((float) $record->precio_kilo, 2) .
                                        ' = $' .
                                        number_format((float) $record->total, 2);
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MÓVIL --}}
            <div class="mobile-items">
                @foreach ($record->items as $item)
                    @php
                        $precioConfig = $item->prenda?->precios()->where('sucursal_id', $record->sucursal_id)->first();

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
                            $explicacion =
                                'Prenda registrada solo como control. El cobro fue por ' .
                                number_format((float) $record->kilos, 2) .
                                ' kg × $' .
                                number_format((float) $record->precio_kilo, 2) .
                                ' = $' .
                                number_format((float) $record->total, 2);
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

                                    $explicacion = implode(' + ', $partes) . ' = $' . number_format($item->subtotal, 2);
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
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PAGOS --}}
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

                    {{-- Información izquierda --}}
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

                    {{-- Monto y acciones --}}
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

        <div class="ticket-card" style="margin-top:40px;">
            <h3 style="font-weight:700; margin-bottom:20px; color:#ffffff;">
                Procesos
            </h3>

            @forelse($record->procesos as $proceso)
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

                        @if (!$completado)
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

    </div>
</x-filament-panels::page>
