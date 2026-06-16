<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de dotaciones - {{ $producto->nombre }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
            padding: 24px;
        }
        .header {
            margin-bottom: 18px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 12px;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 6px 0;
        }
        .sub {
            color: #6b7280;
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        th, td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f9fafb;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .totals {
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }
        .box {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            flex: 1;
        }
        .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .value {
            font-size: 15px;
            font-weight: 700;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    @php
        $dotaciones = $producto->dotaciones ?? collect();
        $totalUnidades = $dotaciones->sum('cantidad');
        $totalInvertido = $dotaciones->sum('total');
    @endphp

    <div class="header">
        <h1 class="title">Historial de dotaciones</h1>
        <div class="sub"><strong>Producto:</strong> {{ $producto->nombre }}</div>
        <div class="sub"><strong>Sucursal:</strong> {{ $producto->sucursal->nombre ?? 'N/A' }}</div>
        <div class="sub"><strong>Precio de venta:</strong> ${{ number_format((float) $producto->precio_base, 2) }}</div>
        <div class="sub"><strong>Precio de compra actual:</strong> ${{ number_format((float) $producto->precio_compra, 2) }}</div>
    </div>

    <div class="totals">
        <div class="box">
            <div class="label">Unidades dotadas</div>
            <div class="value">{{ number_format((int) $totalUnidades) }}</div>
        </div>
        <div class="box">
            <div class="label">Total invertido</div>
            <div class="value">${{ number_format((float) $totalInvertido, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Sucursal</th>
                <th>Cantidad</th>
                <th>Compra unit.</th>
                <th>Total</th>
                <th>Registrado por</th>
                <th>Nota</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dotaciones as $dotacion)
                <tr>
                    <td>{{ $dotacion->created_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $dotacion->sucursal->nombre ?? 'N/A' }}</td>
                    <td>{{ number_format((int) $dotacion->cantidad) }}</td>
                    <td>${{ number_format((float) $dotacion->precio_compra, 2) }}</td>
                    <td>${{ number_format((float) $dotacion->total, 2) }}</td>
                    <td>{{ $dotacion->usuario->name ?? 'N/A' }}</td>
                    <td>{{ $dotacion->nota ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay dotaciones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
