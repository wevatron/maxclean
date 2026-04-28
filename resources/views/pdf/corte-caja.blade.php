<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 30px;
        }

        .header {
            margin-bottom: 25px;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .meta {
            font-size: 12px;
            color: #4b5563;
            line-height: 1.6;
        }

        .summary {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .summary td {
            width: 25%;
            padding: 14px;
            border-radius: 8px;
            vertical-align: top;
        }

        .box-gray { background: #f3f4f6; }
        .box-green { background: #ecfdf5; }
        .box-red { background: #fee2e2; }
        .box-blue { background: #e0f2fe; }

        .label {
            font-size: 11px;
            color: #374151;
            margin-bottom: 6px;
        }

        .value {
            font-size: 16px;
            font-weight: bold;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.main th {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }

        table.main td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="header">
            <div class="title">Corte de Caja #{{ $corte->id }}</div>

            <div class="meta">
                <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($corte->fecha)->format('d/m/Y') }}<br>
                <strong>Turno:</strong> {{ ucfirst($corte->turno) }}<br>
                <strong>Sucursal:</strong> {{ $corte->sucursal->nombre ?? 'N/A' }}<br>
                <strong>Operador:</strong> {{ $corte->operador->name ?? 'N/A' }}<br>
                <strong>Cerrado en:</strong> {{ $corte->cerrado_en ? \Carbon\Carbon::parse($corte->cerrado_en)->format('d/m/Y H:i') : 'N/A' }}
            </div>
        </div>

        @php
            $pagos = $corte->pagos ?? collect();

            $ventas = $pagos->filter(fn ($p) => ($p->tipo_movimiento ?? 'venta') === 'venta')->sum('monto');
            $dotaciones = $pagos->where('tipo_movimiento', 'dotacion')->sum('monto');
            $gastos = $pagos->where('tipo_movimiento', 'gasto')->sum('monto');
            $saldo = ($ventas + $dotaciones) - $gastos;
        @endphp

        <table class="summary">
            <tr>
                <td class="box-gray">
                    <div class="label">Ventas</div>
                    <div class="value">${{ number_format($ventas, 2) }}</div>
                </td>

                <td class="box-green">
                    <div class="label">Dotaciones</div>
                    <div class="value">${{ number_format($dotaciones, 2) }}</div>
                </td>

                <td class="box-red">
                    <div class="label">Gastos</div>
                    <div class="value">${{ number_format($gastos, 2) }}</div>
                </td>

                <td class="box-blue">
                    <div class="label">Saldo en Caja</div>
                    <div class="value">${{ number_format($saldo, 2) }}</div>
                </td>
            </tr>
        </table>

        <table class="main">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Método de pago</th>
                    <th>Descripción</th>
                    <th class="text-right">Monto</th>
                    <th class="text-center">Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagos as $pago)
                    <tr>
                        <td>
                            {{ ucfirst($pago->tipo_movimiento ?? 'venta') }}
                        </td>
                        <td>
                            {{ $pago->metodo_pago ?? '-' }}
                        </td>
                        <td>
                            {{ $pago->descripcion ?? $pago->ticket->tipo ?? '-' }}
                        </td>
                        <td class="text-right">
                            ${{ number_format($pago->monto, 2) }}
                        </td>
                        <td class="text-center">
                            {{ $pago->created_at ? $pago->created_at->format('H:i') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            No hay movimientos registrados en este corte.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Documento generado automáticamente por el sistema.
        </div>
    </div>
</body>
</html>