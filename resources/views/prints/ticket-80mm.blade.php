<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $ticket->numero }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 3mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #000000;
            font-family: "Courier New", Courier, monospace;
            font-size: 12px;
            line-height: 1.35;
        }

        body {
            width: 80mm;
        }

        .ticket {
            width: 74mm;
            margin: 0 auto;
            padding: 2mm 0;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: 700;
        }

        .small {
            font-size: 10px;
        }

        .tiny {
            font-size: 9px;
        }

        .mt-1 {
            margin-top: 4px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mt-3 {
            margin-top: 12px;
        }

        .mb-1 {
            margin-bottom: 4px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: flex-start;
        }

        .row .left {
            flex: 1 1 auto;
            min-width: 0;
        }

        .row .right {
            flex: 0 0 auto;
            white-space: nowrap;
            text-align: right;
        }

        .item {
            margin-bottom: 7px;
        }

        .item-name {
            font-weight: 700;
            word-break: break-word;
        }

        .mono-box {
            border: 1px dashed #000;
            padding: 6px;
            margin-top: 8px;
        }

        .no-print {
            text-align: center;
            padding: 10px 0 16px;
        }

        .no-print button {
            border: 1px solid #000;
            background: #fff;
            color: #000;
            padding: 8px 12px;
            font-family: inherit;
            font-size: 12px;
            cursor: pointer;
            margin: 0 4px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            html,
            body {
                width: 80mm;
            }
        }
    </style>
</head>

<body>
    @php
        $esAutoservicio = $ticket->tipo === 'autoservicio';

        $tipoTexto = match ($ticket->tipo) {
            'encargo_express' => 'ENCARGO EXPRESS',
            'encargo_kilo' => 'POR KILO',
            'autoservicio' => 'AUTOSERVICIO',
            default => 'ENCARGO',
        };

        $tipoLavadoTexto = match ($ticket->tipo_lavado_kilo ?? null) {
            'basico' => 'Básico',
            'premium' => 'Premium',
            'extra_lavado' => 'Extra lavado',
            'expres' => 'Expres',
            'ropa_interior' => 'Ropa interior',
            default => 'Sin especificar',
        };

        $tasaIva = 0.16;
        $totalFiscal = (float) $ticket->total;
        $subtotalFiscal = round($totalFiscal / (1 + $tasaIva), 2);
        $ivaFiscal = round($totalFiscal - $subtotalFiscal, 2);

        $pagosValidos = $ticket->pagos->where('metodo_pago', '!=', 'cancelado')->sortBy('created_at')->values();

        $ultimoPago = $pagosValidos->last();
        $montoUltimoPago = (float) ($ultimoPago->monto ?? 0);
        $saldoActual = max(0, (float) $ticket->saldo);
        $debeAntesDeEstePago = max(0, $saldoActual + $montoUltimoPago);

        $leyendaPago = 'SIN PAGO REGISTRADO';

        if ($ultimoPago) {
            $leyendaPago = $saldoActual <= 0 ? 'LIQUIDACIÓN DE CUENTA' : 'PAGO PARCIAL';
        }
    @endphp

    <div class="ticket">
        <div class="no-print">
            <button onclick="window.print()">Imprimir</button>
            <button onclick="window.close()">Cerrar</button>
        </div>

        <div class="center bold" style="font-size:16px;">
            MAXCLEAN
        </div>

        <div class="center small mt-1">
            {{ $ticket->sucursal->nombre ?? 'Sucursal' }}
        </div>

        <div class="center small">
            Ticket #{{ $ticket->numero }}
        </div>

        <div class="center small">
            {{ $ticket->created_at?->format('d/m/Y H:i') }}
        </div>

        <div class="divider"></div>

        <div class="row">
            <div class="left bold">Tipo</div>
            <div class="right">{{ $tipoTexto }}</div>
        </div>

        <div class="row">
            <div class="left bold">Estado</div>
            <div class="right">{{ strtoupper($ticket->status->nombre ?? 'SIN ESTADO') }}</div>
        </div>

        <div class="row">
            <div class="left bold">Cliente</div>
            <div class="right">{{ $ticket->cliente->name ?? 'Sin cliente' }}</div>
        </div>

        <div class="row">
            <div class="left bold">Operador</div>
            <div class="right">{{ $ticket->operador->name ?? 'Sin operador' }}</div>
        </div>

        @if ($ticket->tipo === 'encargo_kilo')
            <div class="row mt-1">
                <div class="left bold">Kilos</div>
                <div class="right">{{ number_format((float) $ticket->kilos, 2) }} kg</div>
            </div>

            <div class="row">
                <div class="left bold">Lavado</div>
                <div class="right">{{ $tipoLavadoTexto }}</div>
            </div>

            <div class="row">
                <div class="left bold">Precio/kg</div>
                <div class="right">${{ number_format((float) $ticket->precio_kilo, 2) }}</div>
            </div>
        @endif

        <div class="divider"></div>

        @if ($esAutoservicio)
            <div class="bold mb-2">SERVICIOS</div>

            @forelse ($ticket->servicios as $servicio)
                @php
                    $cantidad = (int) ($servicio->pivot->cantidad ?? 1);
                    $precioUnitario = (float) ($servicio->pivot->precio_unitario ?? ($servicio->precio_base ?? 0));
                    $subtotal = (float) ($servicio->pivot->subtotal ?? $cantidad * $precioUnitario);
                @endphp

                <div class="item">
                    <div class="item-name">{{ $servicio->nombre ?? 'Sin servicio' }}</div>

                    @if (!empty($servicio->descripcion))
                        <div class="tiny">{{ $servicio->descripcion }}</div>
                    @endif

                    <div class="row small">
                        <div class="left">
                            {{ $cantidad }} x ${{ number_format($precioUnitario, 2) }}
                        </div>
                        <div class="right bold">
                            ${{ number_format($subtotal, 2) }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="small">Sin servicios registrados.</div>
            @endforelse
        @else
            <div class="bold mb-2">PRENDAS</div>

            @forelse ($ticket->items as $item)
                @php
                    $cantidad = (int) ($item->cantidad ?? 0);
                    $precioUnitario = (float) ($item->precio_unitario ?? 0);
                    $subtotal = (float) ($item->subtotal ?? 0);
                @endphp

                <div class="item">
                    <div class="item-name">{{ $item->prenda->nombre ?? 'Sin prenda' }}</div>

                    <div class="row small">
                        <div class="left">
                            {{ $cantidad }} x ${{ number_format($precioUnitario, 2) }}
                        </div>
                        <div class="right bold">
                            ${{ number_format($subtotal, 2) }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="small">
                    {{ $ticket->tipo === 'encargo_kilo' ? 'Sin inventario de prendas registrado.' : 'Sin prendas registradas.' }}
                </div>
            @endforelse
        @endif


        @if ($saldoActual > 0)
            <div class="mono-box">
                <div class="center bold" style="margin-bottom:6px;">
                    {{ $ultimoPago ? 'PAGO PARCIAL' : 'SIN PAGO REGISTRADO' }}
                </div>

                @if ($ultimoPago)
                    <div class="row">
                        <div class="left bold">Debe antes de este pago</div>
                        <div class="right">${{ number_format($debeAntesDeEstePago, 2) }}</div>
                    </div>

                    <div class="row">
                        <div class="left bold">Pago recibido</div>
                        <div class="right">${{ number_format($montoUltimoPago, 2) }}</div>
                    </div>
                @endif

                <div class="row">
                    <div class="left bold">Debe ahora</div>
                    <div class="right">${{ number_format($saldoActual, 2) }}</div>
                </div>
            </div>
        @endif

        <div class="divider"></div>

        <div class="row">
            <div class="left bold">SUBTOTAL</div>
            <div class="right bold">${{ number_format($subtotalFiscal, 2) }}</div>
        </div>

        <div class="row">
            <div class="left bold">IVA 16%</div>
            <div class="right bold">${{ number_format($ivaFiscal, 2) }}</div>
        </div>

        <div class="row">
            <div class="left bold">TOTAL</div>
            <div class="right bold">${{ number_format($totalFiscal, 2) }}</div>
        </div>

        <div class="row">
            <div class="left bold">PAGADO</div>
            <div class="right bold">${{ number_format((float) $ticket->pagado, 2) }}</div>
        </div>

        <div class="row">
            <div class="left bold">SALDO</div>
            <div class="right bold">${{ number_format((float) $ticket->saldo, 2) }}</div>
        </div>

        <div class="divider"></div>

        <div class="bold mb-2">PAGOS</div>

        @forelse ($ticket->pagos as $pago)
            <div class="row small">
                <div class="left">
                    {{ ucfirst($pago->metodo_pago) }}
                    @if ($pago->metodo_pago === 'cancelado')
                        (cancelado)
                    @endif
                    <div class="tiny">{{ $pago->created_at?->format('d/m H:i') }}</div>
                </div>

                <div class="right">
                    {{ $pago->monto < 0 ? '-' : '+' }}${{ number_format(abs((float) $pago->monto), 2) }}
                </div>
            </div>
        @empty
            <div class="small">Sin pagos registrados.</div>
        @endforelse

        {{--         @if (!$esAutoservicio && $ticket->procesos->isNotEmpty())
            <div class="divider"></div>

            <div class="bold mb-2">PROCESOS</div>

            @foreach ($ticket->procesos as $proceso)
                <div class="row small">
                    <div class="left">{{ ucfirst($proceso->proceso) }}</div>
                    <div class="right">{{ $proceso->completado ? 'OK' : 'PEND' }}</div>
                </div>
            @endforeach
        @endif --}}

        <div class="divider"></div>

        <div class="center small">
            Gracias por su preferencia
        </div>

        <div class="center tiny mt-1">
            Conserve este ticket para recoger su pedido
        </div>

        <div style="height: 12mm;"></div>
    </div>

    @if ($autoPrint)
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 250);
            });
        </script>
    @endif
</body>

</html>
