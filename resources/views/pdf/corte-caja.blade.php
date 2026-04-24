<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Corte de Caja</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .muted {
            color: #666;
            font-size: 11px;
        }

        .box {
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 14px;
            border-radius: 6px;
        }

        .summary {
            width: 100%;
            margin-bottom: 16px;
        }

        .summary td {
            border: 1px solid #ddd;
            padding: 10px;
            width: 25%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f2f2f2;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        td {
            padding: 7px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 11px;
        }

        .right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div style="width:100%; margin-bottom:14px;">
        <div style="float:left; width:140px;">
            <img
                src="{{ public_path('img/logo.png') }}"
                style="width:130px; height:auto;"
            >
        </div>

        <div style="margin-left:160px;">
            <h1>Corte de Caja #{{ $corte->id }}</h1>

            <div class="muted">
                Fecha: {{ $corte->fecha?->format('d/m/Y') }} |
                Turno: {{ ucfirst($corte->turno) }} |
                Sucursal: {{ $corte->sucursal->nombre ?? 'Sin sucursal' }} |
                Operador: {{ $corte->operador->name ?? 'Sin operador' }}
            </div>
        </div>

        <div style="clear:both;"></div>
    </div>

    <br>

    <table class="summary">
        <tr>
            <td>
                <strong>Total</strong><br>
                ${{ number_format($corte->total ?? 0, 2) }}
            </td>
            <td>
                <strong>Efectivo</strong><br>
                ${{ number_format($corte->total_efectivo ?? 0, 2) }}
            </td>
            <td>
                <strong>Tarjeta</strong><br>
                ${{ number_format($corte->total_tarjeta ?? 0, 2) }}
            </td>
            <td>
                <strong>Transferencia</strong><br>
                ${{ number_format($corte->total_transferencia ?? 0, 2) }}
            </td>
        </tr>
    </table>

    <h3>Pagos incluidos</h3>

    <table>
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Cliente</th>
                <th>Modo</th>
                <th>Lavado</th>
                <th>Pago</th>
                <th class="right">Monto</th>
                <th>Hora</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($corte->pagos as $pago)
                @php
                    $ticket = $pago->ticket;

                    $modoTexto = match ($ticket?->tipo) {
                        'encargo_express' => 'Express',
                        'encargo_kilo' => 'Por kilo',
                        default => 'Por encargo',
                    };

                    $lavadoTexto = match ($ticket?->tipo_lavado_kilo) {
                        'basico' => 'Básico',
                        'premium' => 'Premium',
                        'extra_lavado' => 'Extra lavado',
                        default => '-',
                    };
                @endphp

                <tr>
                    <td>
                        @if ($ticket)
                            #{{ str_pad($ticket->numero, 6, '0', STR_PAD_LEFT) }}
                        @else
                            Sin ticket
                        @endif
                    </td>

                    <td>{{ $ticket?->cliente?->name ?? 'Sin cliente' }}</td>

                    <td>{{ $modoTexto }}</td>

                    <td>
                        @if ($ticket?->tipo === 'encargo_kilo')
                            {{ $lavadoTexto }}<br>
                            {{ number_format((float) $ticket?->kilos, 2) }} kg<br>
                            ${{ number_format((float) $ticket?->precio_kilo, 2) }}/kg
                        @else
                            -
                        @endif
                    </td>

                    <td>{{ ucfirst($pago->metodo_pago) }}</td>

                    <td class="right">
                        ${{ number_format($pago->monto, 2) }}
                    </td>

                    <td>
                        {{ $pago->created_at?->format('H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;">
                        No hay pagos en este corte.
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <td colspan="5" class="right total">Total</td>
                <td class="right total">
                    ${{ number_format($corte->pagos->sum('monto'), 2) }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>