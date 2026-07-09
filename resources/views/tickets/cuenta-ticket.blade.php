<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cuenta {{ $cuenta->numero }}</title>

    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            width: 80mm;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
        }

        .ticket {
            width: 80mm;
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .title {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .subtitle {
            font-size: 12px;
            margin-bottom: 3px;
        }

        .section {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #000;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px;
        }

        .row .left {
            flex: 1;
        }

        .row .right {
            min-width: 70px;
            text-align: right;
        }

        .small {
            font-size: 10px;
        }

        .muted {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th {
            font-size: 10px;
            text-align: left;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        td {
            font-size: 10px;
            padding: 3px 0;
            vertical-align: top;
        }

        .total-box {
            margin-top: 10px;
            padding: 8px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        .saldo {
            font-size: 16px;
            font-weight: 800;
        }

        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 10px;
        }

        .account-notes {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #000;
            font-size: 10px;
            line-height: 1.4;
        }

        .account-notes-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .fiscal-note {
            margin-top: 10px;
            font-size: 9px;
            line-height: 1.45;
            text-align: center;
        }

        .no-print {
            margin: 10px;
            display: flex;
            gap: 8px;
        }

        .no-print button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-print {
            background: #16a34a;
            color: white;
        }

        .btn-close {
            background: #6b7280;
            color: white;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                width: 80mm;
            }

            .ticket {
                padding: 8px;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Imprimir</button>
        <button class="btn-close" onclick="window.close()">Cerrar</button>
    </div>

    <div class="ticket">

        <div class="">
            <div class=" center">
                <div class="center">
                    <div class="title">MAX & CLEAN</div>
                    <div class="subtitle">Estado de cuenta</div>

                    @if ($cuenta->sucursal)
                        <div class="small">{{ $cuenta->sucursal->nombre }}</div>
                    @endif

                    <div class="small">
                        {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div style="margin-top:8px;">
                    <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" alt="QR Cuenta {{ $cuenta->id }}"
                        style="width:130px; height:130px;">
                </div>

                <div class="small" style="margin-top:4px;">
                    #: {{ str_pad($cuenta->id, 6, '0', STR_PAD_LEFT) }}
                </div>
            </div>

            <div class="row section">
                <div class="left bold">Cliente</div>
                <div class="right">{{ $cuenta->cliente?->name ?? 'Sin cliente' }}</div>
            </div>

            @if ($cuenta->cliente?->whatsapp)
                <div class="row">
                    <div class="left bold">Whatsapp</div>
                    <div class="right">{{ $cuenta->cliente->whatsapp }}</div>
                </div>
            @endif

            <div class="row">
                <div class="left bold">Estatus</div>
                <div class="right">{{ ucfirst($cuenta->estatus) }}</div>
            </div>

            <div class="row">
                <div class="left bold">Abierta</div>
                <div class="right">
                    {{ $cuenta->abierta_en ? $cuenta->abierta_en->format('d/m/Y H:i') : $cuenta->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="section">
            <div class="bold center">TICKETS INCLUIDOS</div>

            <table>
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Tipo</th>
                        <th>Unidad</th>
                        <th class="right">Total</th>
                        <th class="right">Desc.</th>
                        <th class="right">Pagado</th>
                        <th class="right">Debe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        @php
                            $pagadoTicket = $ticket->pagos->where('cancelado', false)->sum('monto');
                            $saldoTicket = max((float) $ticket->total - (float) $pagadoTicket, 0);
                            $descuentoTicket = (float) ($ticket->descuento_aplicado ?? 0);

                            $tipoTicket = match ($ticket->tipo) {
                                'encargo' => 'Pieza',
                                'encargo_express' => 'Express',
                                'encargo_kilo' => 'Kilo',
                                'autoservicio' => 'Auto',
                                default => $ticket->tipo,
                            };
                        @endphp

                        <tr>
                            <td>#{{ $ticket->numero }}</td>
                            <td>{{ $tipoTicket }}</td>
                            <td>{{ $ticket->unidad ?? '-' }}</td>
                            <td class="right">${{ number_format($ticket->total, 2) }}</td>
                            <td class="right">${{ number_format($descuentoTicket, 2) }}</td>
                            <td class="right">${{ number_format($pagadoTicket, 2) }}</td>
                            <td class="right">${{ number_format($saldoTicket, 2) }}</td>
                        </tr>

                        @if ($ticket->tipo === 'autoservicio' && $ticket->desglose_autoservicio->isNotEmpty())
                            <tr>
                                <td colspan="7" style="padding: 0 0 6px 0;">
                                    <div style="font-size: 9px; line-height: 1.35; border-left: 1px dashed #000; padding-left: 6px; margin-left: 2px;">
                                        <div class="bold" style="margin-bottom: 2px;">Desglose</div>

                                        @foreach ($ticket->desglose_autoservicio as $detalle)
                                            <div class="row" style="margin-bottom: 1px;">
                                                <div class="left">
                                                    {{ $detalle['tipo'] === 'servicio' ? 'Serv.' : 'Prod.' }}:
                                                    {{ $detalle['nombre'] }}
                                                </div>
                                                <div class="right">
                                                    {{ $detalle['cantidad'] }} x ${{ number_format($detalle['precio_unitario'], 2) }}
                                                    = ${{ number_format($detalle['subtotal'], 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="bold center">PAGOS APLICADOS</div>

            @if ($pagosAplicados->count())
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Ticket</th>
                            <th>Método</th>
                            <th class="right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pagosAplicados as $pago)
                            <tr>
                                <td>{{ $pago->created_at->format('d/m H:i') }}</td>
                                <td>#{{ $pago->ticket?->numero ?? 'S/I' }}</td>
                                <td>{{ ucfirst($pago->metodo_pago) }}</td>
                                <td class="right">${{ number_format($pago->monto, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="center small muted" style="margin-top:6px;">
                    Sin pagos registrados.
                </div>
            @endif
        </div>

        @if (filled($cuenta->notas))
            <div class="account-notes">
                <div class="account-notes-title">Notas de la cuenta</div>
                <div>{!! nl2br(e($cuenta->notas)) !!}</div>
            </div>
        @endif

        <div class="total-box">
            <div class="row">
                <div class="left bold">Total antes de descuento</div>
                <div class="right bold">${{ number_format($totalAntesDescuento, 2) }}</div>
            </div>

            <div class="row">
                <div class="left bold">Descuentos aplicados</div>
                <div class="right bold">-${{ number_format($totalDescuentos, 2) }}</div>
            </div>

            <div class="row">
                <div class="left bold">Total tickets</div>
                <div class="right bold">${{ number_format($totalTickets, 2) }}</div>
            </div>

            <div class="row">
                <div class="left bold">Total pagado</div>
                <div class="right bold">${{ number_format($totalPagado, 2) }}</div>
            </div>

            <div class="row saldo">
                <div class="left">Saldo</div>
                <div class="right">${{ number_format($saldo, 2) }}</div>
            </div>
        </div>

        @if ($saldo <= 0)
            <div class="section center bold">
                CUENTA LIQUIDADA
            </div>
        @else
            <div class="section center bold">
                SALDO PENDIENTE: ${{ number_format($saldo, 2) }}
            </div>
        @endif

        <div class="fiscal-note">
            En caso de requerir factura, solicítela el día de su pago; de lo contrario, se integrará a la factura global del día.
        </div>

        <div class="footer">
            Gracias por su preferencia.
            <br>
            Este comprobante resume los tickets agrupados en la cuenta.
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>

</body>

</html>
