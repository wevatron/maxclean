<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja</title>
    <style>
        @page {
            margin: 14mm 12mm;
        }

        body {
            font-family: Montserrat, "Helvetica Neue", Arial, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        .container {
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
            line-height: 1.1;
            color: #0f172a;
        }

        .meta {
            font-size: 10px;
            color: #475569;
            line-height: 1.45;
        }

        .brand-logo {
            width: 88px;
            height: auto;
            object-fit: contain;
            display: block;
            margin-bottom: 8px;
        }

        .panel-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .panel-grid td {
            width: 33.333%;
            padding: 0 6px 8px 0;
            vertical-align: top;
        }

        .metric {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 14px;
            background: #ffffff;
            min-height: 82px;
        }

        .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .value {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.1;
            color: #0f172a;
        }

        .value-muted {
            font-size: 12px;
            color: #475569;
            margin-top: 6px;
        }

        .accent {
            border-left: 3px solid #cbd5e1;
            padding-left: 10px;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.main th {
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            padding: 9px 8px;
            text-align: left;
            font-size: 9px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        table.main td {
            border-bottom: 1px solid #eef2f7;
            padding: 9px 8px;
            font-size: 10px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #64748b;
        }

        .section-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: #64748b;
            font-weight: 800;
            margin: 14px 0 8px;
        }

        .footer {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .summary-note {
            font-size: 10px;
            color: #64748b;
        }

        .table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .table-wrap table {
            margin-top: 0;
        }

        .table-wrap table thead th {
            border-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        @php
            $logoPath = public_path('img/logo.png');
            $logoData = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : null;
        @endphp

        <div class="header">
            <div>
                @if ($logoData)
                    <img src="{{ $logoData }}" alt="Max&Clean" class="brand-logo">
                @endif
                <div class="title">Corte de caja #{{ $corte->id }}</div>
                <div class="meta accent">
                    <div><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($corte->fecha)->format('d/m/Y') }}</div>
                    <div><strong>Turno:</strong> {{ ucfirst($corte->turno) }}</div>
                    <div><strong>Sucursal:</strong> {{ $corte->sucursal->nombre ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="meta" style="text-align:right; min-width: 220px;">
                <div><strong>Operador:</strong> {{ $corte->operador->name ?? 'N/A' }}</div>
                <div><strong>Cerrado en:</strong> {{ $corte->cerrado_en ? \Carbon\Carbon::parse($corte->cerrado_en)->format('d/m/Y H:i') : 'N/A' }}</div>
            </div>
        </div>

        @php
            $pagos = $corte->pagos ?? collect();

            $ventas = $pagos->filter(fn ($p) => ($p->tipo_movimiento ?? 'venta') === 'venta')->sum('monto');
            $dotaciones = $pagos->where('tipo_movimiento', 'dotacion')->sum('monto');
            $gastos = $pagos->where('tipo_movimiento', 'gasto')->sum('monto');
            $saldoGlobal = ($ventas + $dotaciones) - $gastos;

            $efectivo = $pagos->where('tipo_movimiento', 'venta')->where('metodo_pago', 'efectivo')->sum('monto');
            $tarjeta = $pagos->where('tipo_movimiento', 'venta')->where('metodo_pago', 'tarjeta')->sum('monto');
            $transferencia = $pagos->where('tipo_movimiento', 'venta')->where('metodo_pago', 'transferencia')->sum('monto');
            $saldoCaja = ($efectivo + $dotaciones) - $gastos;
            $bancos = $tarjeta + $transferencia;

            $metodosOrden = ['efectivo', 'tarjeta', 'transferencia'];
            $pagosVentas = $pagos->filter(fn ($p) => ($p->tipo_movimiento ?? 'venta') === 'venta');
            $pagosPorMetodo = $pagosVentas->groupBy(fn ($p) => strtolower($p->metodo_pago ?? 'otros'));
            $otrosMovimientos = $pagos->filter(fn ($p) => ($p->tipo_movimiento ?? 'venta') !== 'venta');
        @endphp

        <div class="section-title">Resumen</div>
        <table class="panel-grid">
            <tr>
                <td>
                    <div class="metric">
                        <div class="label">Ventas</div>
                        <div class="value">${{ number_format($ventas, 2) }}</div>
                        <div class="value-muted">Ingresos registrados en el corte</div>
                    </div>
                </td>

                <td>
                    <div class="metric">
                        <div class="label">Dotaciones</div>
                        <div class="value">${{ number_format($dotaciones, 2) }}</div>
                        <div class="value-muted">Entradas a caja</div>
                    </div>
                </td>

                <td>
                    <div class="metric">
                        <div class="label">Gastos</div>
                        <div class="value">${{ number_format($gastos, 2) }}</div>
                        <div class="value-muted">Salidas de caja</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="metric">
                        <div class="label">Saldo global</div>
                        <div class="value">${{ number_format($saldoGlobal, 2) }}</div>
                        <div class="value-muted">Ventas + dotaciones - gastos</div>
                    </div>
                </td>

                <td>
                    <div class="metric">
                        <div class="label">Saldo en caja</div>
                        <div class="value">${{ number_format($saldoCaja, 2) }}</div>
                        <div class="value-muted">Disponible al cierre</div>
                    </div>
                </td>

                <td>
                    <div class="metric">
                        <div class="label">Bancos</div>
                        <div class="value">${{ number_format($bancos, 2) }}</div>
                        <div class="value-muted">Tarjetas + transferencias</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Detalle de movimientos</div>

        @foreach($metodosOrden as $metodoClave)
            @php
                $movimientosMetodo = $pagosPorMetodo->get($metodoClave, collect());
                $montoMetodo = $movimientosMetodo->sum('monto');
                $nombreMetodo = ucfirst($metodoClave);
            @endphp

            @if ($movimientosMetodo->isNotEmpty())
                <div class="section-title" style="margin-top: 12px;">{{ $nombreMetodo }}</div>
                <div class="table-wrap">
                    <table class="main">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th class="text-right">Monto</th>
                                <th class="text-center">Hora</th>
                                <th class="text-center">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientosMetodo as $pago)
                                @php
                                    $tipo = ucfirst($pago->tipo_movimiento ?? 'venta');
                                    $descripcion = $pago->descripcion ?: data_get($pago, 'ticket.tipo', '-');
                                @endphp
                                <tr>
                                    <td>{{ $tipo }}</td>
                                    <td>
                                        {{ $descripcion }}
                                        @if ($pago->ticket)
                                            <div class="muted">Ticket #{{ $pago->ticket->numero }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right">${{ number_format($pago->monto, 2) }}</td>
                                    <td class="text-center">
                                        {{ $pago->created_at ? $pago->created_at->format('H:i') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $pago->created_at ? $pago->created_at->format('d/m/Y') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="meta" style="text-align:right; margin-top: 4px;">
                    <strong>Total {{ $nombreMetodo }}:</strong> ${{ number_format($montoMetodo, 2) }}
                </div>
            @endif
        @endforeach

        @foreach($pagosPorMetodo->keys()->diff($metodosOrden) as $metodoClave)
            @php
                $movimientosMetodo = $pagosPorMetodo->get($metodoClave, collect());
                $montoMetodo = $movimientosMetodo->sum('monto');
                $nombreMetodo = ucfirst($metodoClave);
            @endphp

            @if ($movimientosMetodo->isNotEmpty())
                <div class="section-title" style="margin-top: 12px;">{{ $nombreMetodo }}</div>
                <div class="table-wrap">
                    <table class="main">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th class="text-right">Monto</th>
                                <th class="text-center">Hora</th>
                                <th class="text-center">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientosMetodo as $pago)
                                @php
                                    $tipo = ucfirst($pago->tipo_movimiento ?? 'venta');
                                    $descripcion = $pago->descripcion ?: data_get($pago, 'ticket.tipo', '-');
                                @endphp
                                <tr>
                                    <td>{{ $tipo }}</td>
                                    <td>
                                        {{ $descripcion }}
                                        @if ($pago->ticket)
                                            <div class="muted">Ticket #{{ $pago->ticket->numero }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right">${{ number_format($pago->monto, 2) }}</td>
                                    <td class="text-center">
                                        {{ $pago->created_at ? $pago->created_at->format('H:i') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $pago->created_at ? $pago->created_at->format('d/m/Y') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="meta" style="text-align:right; margin-top: 4px;">
                    <strong>Total {{ $nombreMetodo }}:</strong> ${{ number_format($montoMetodo, 2) }}
                </div>
            @endif
        @endforeach

        @if ($pagos->isEmpty())
            <div class="table-wrap">
                <table class="main">
                    <tbody>
                        <tr>
                            <td class="text-center">
                                No hay movimientos registrados en este corte.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @if ($otrosMovimientos->isNotEmpty())
            <div class="section-title" style="margin-top: 12px;">Otros movimientos</div>
            <div class="table-wrap">
                <table class="main">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th class="text-right">Monto</th>
                            <th class="text-center">Hora</th>
                            <th class="text-center">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($otrosMovimientos as $pago)
                            @php
                                $tipo = ucfirst($pago->tipo_movimiento ?? '-');
                                $descripcion = $pago->descripcion ?: data_get($pago, 'ticket.tipo', '-');
                            @endphp
                            <tr>
                                <td>{{ $tipo }}</td>
                                <td>
                                    {{ $descripcion }}
                                    @if ($pago->ticket)
                                        <div class="muted">Ticket #{{ $pago->ticket->numero }}</div>
                                    @endif
                                </td>
                                <td class="text-right">${{ number_format($pago->monto, 2) }}</td>
                                <td class="text-center">
                                    {{ $pago->created_at ? $pago->created_at->format('H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $pago->created_at ? $pago->created_at->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="footer">
            <div>Documento generado automáticamente por el sistema.</div>
            <div class="summary-note">
                Efectivo: ${{ number_format($efectivo, 2) }} · Tarjeta: ${{ number_format($tarjeta, 2) }} · Transferencia: ${{ number_format($transferencia, 2) }}
            </div>
        </div>
    </div>
</body>
</html>
