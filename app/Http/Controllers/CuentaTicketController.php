<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\TicketPago;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CuentaTicketController extends Controller
{
    public function show(Cuenta $cuenta)
    {
        $cuenta->load([
            'cliente',
            'sucursal',
            'operador',
            'pagos.operador',
            'tickets' => function ($query) {
                $query->with([
                    'status',
                    'items',
                    'productos',
                    'servicios',
                    'pagos' => function ($subQuery) {
                        $subQuery
                            ->where('cancelado', false)
                            ->orderBy('created_at');
                    },
                ])
                    ->orderBy('id');
            },
        ]);

        $tickets = $cuenta->tickets->map(function ($ticket) {
            $unidad = match ($ticket->tipo) {
                'encargo_kilo' => number_format((float) $ticket->kilos, 2) . ' kg',
                'encargo', 'encargo_express' => $ticket->items->sum('cantidad') . ' piezas',
                'autoservicio' => (
                    (int) $ticket->productos->sum(fn ($producto) => (int) ($producto->pivot->cantidad ?? 1))
                    + (int) $ticket->servicios->sum(fn ($servicio) => (int) ($servicio->pivot->cantidad ?? 1))
                ) . ' prod/serv',
                default => '-',
            };

            $ticket->unidad = $unidad;

            $ticket->desglose_autoservicio = $ticket->tipo === 'autoservicio'
                ? $ticket->servicios
                    ->map(function ($servicio) {
                        $cantidad = (int) ($servicio->pivot->cantidad ?? 1);
                        $precioUnitario = (float) ($servicio->pivot->precio_unitario ?? ($servicio->precio_base ?? 0));

                        return [
                            'tipo' => 'servicio',
                            'nombre' => $servicio->nombre ?? 'Sin servicio',
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precioUnitario,
                            'subtotal' => (float) ($servicio->pivot->subtotal ?? ($cantidad * $precioUnitario)),
                        ];
                    })
                    ->concat($ticket->productos->map(function ($producto) {
                        $cantidad = (int) ($producto->pivot->cantidad ?? 1);
                        $precioUnitario = (float) ($producto->pivot->precio_unitario ?? ($producto->precio_base ?? 0));

                        return [
                            'tipo' => 'producto',
                            'nombre' => $producto->nombre ?? 'Sin producto',
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precioUnitario,
                            'subtotal' => (float) ($producto->pivot->subtotal ?? ($cantidad * $precioUnitario)),
                        ];
                    }))
                    ->values()
                : collect();

            return $ticket;
        });

        $totalTickets = $tickets->sum('total');
        $totalDescuentos = $tickets->sum(fn ($ticket) => (float) ($ticket->descuento_aplicado ?? 0));
        $totalAntesDescuento = $totalTickets + $totalDescuentos;

        $totalPagado = TicketPago::query()
            ->where('cuenta_id', $cuenta->id)
            ->where('cancelado', false)
            ->sum('monto');

        $saldo = max((float) $totalTickets - (float) $totalPagado, 0);

        $pagosAplicados = TicketPago::query()
            ->with('ticket')
            ->where('cuenta_id', $cuenta->id)
            ->where('cancelado', false)
            ->orderBy('created_at')
            ->get();

        $qrContenido = (string) $cuenta->id;

        $qrSvg = QrCode::format('svg')
            ->size(130)
            ->margin(1)
            ->generate($qrContenido);

        $qrBase64 = base64_encode($qrSvg);

        return view('tickets.cuenta-ticket', [
            'cuenta' => $cuenta,
            'tickets' => $tickets,
            'pagosAplicados' => $pagosAplicados,
            'totalTickets' => $totalTickets,
            'totalDescuentos' => $totalDescuentos,
            'totalAntesDescuento' => $totalAntesDescuento,
            'totalPagado' => $totalPagado,
            'saldo' => $saldo,
            'qrContenido' => $qrContenido,
            'qrBase64' => $qrBase64,
        ]);
    }
}
